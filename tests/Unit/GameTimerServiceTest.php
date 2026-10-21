<?php

namespace Tests\Unit;

use App\Events\AdminDashboardUpdated;
use App\Events\Top10Updated;
use App\Services\GameTimerService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\Concerns\CreatesGameData;
use Tests\TestCase;

class GameTimerServiceTest extends TestCase
{
    use CreatesGameData;
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_elapsed_time_subtracts_paused_time_and_adds_penalties(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-05 12:00:00'));

        $partida = $this->createPartida([
            'inicio_juego' => Carbon::now()->subMinutes(10),
            'estado' => 'pausado',
            'tiempo_pausado_segundos' => 120,
            'ultima_pausa_at' => Carbon::now()->subMinute(),
            'penalizacion_segundos' => 30,
        ]);

        session($this->gameSessionFor($partida));

        $service = app(GameTimerService::class);

        $this->assertSame(450, $service->obtenerTiempoTranscurrido());
    }

    public function test_timeout_persists_the_final_time_and_timeout_phase(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-05 12:00:00'));
        Event::fake([Top10Updated::class, AdminDashboardUpdated::class]);

        $partida = $this->createPartida([
            'inicio_juego' => Carbon::now()->subMinutes(91),
            'fase_actual' => config('fases.fases.prueba6.db'),
        ]);

        session($this->gameSessionFor($partida));

        $service = app(GameTimerService::class);

        $this->assertTrue($service->verificarTimeout());

        $partida->refresh();

        $this->assertSame(config('fases.fases.timeout.db'), $partida->fase_actual);
        $this->assertSame(5460, $partida->tiempo_segundos);
        Event::assertDispatched(Top10Updated::class);
        Event::assertDispatched(AdminDashboardUpdated::class);
    }
}
