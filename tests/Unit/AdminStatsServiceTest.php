<?php

namespace Tests\Unit;

use App\Models\HitoFase;
use App\Services\AdminStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesGameData;
use Tests\TestCase;

class AdminStatsServiceTest extends TestCase
{
    use CreatesGameData;
    use RefreshDatabase;

    public function test_metrics_and_top5_are_calculated_from_final_states_and_penalties(): void
    {
        $equipoConPenalizacion = $this->createPartida([
            'nombre_equipo' => 'Equipo Penalizado',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'tiempo_segundos' => 600,
            'penalizacion_segundos' => 200,
        ]);

        $equipoRapido = $this->createPartida([
            'nombre_equipo' => 'Equipo Rapido',
            'email' => 'rapido@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'tiempo_segundos' => 720,
            'penalizacion_segundos' => 0,
        ]);

        $this->createPartida([
            'email' => 'timeout@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.timeout.db'),
            'tiempo_segundos' => 5400,
        ]);

        $this->createPartida([
            'email' => 'activo@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.prueba6.db'),
            'tiempo_segundos' => 0,
        ]);

        $service = app(AdminStatsService::class);

        $metrics = $service->getMetrics();
        $top5 = $service->getTop5();

        $this->assertSame(4, $metrics['total']);
        $this->assertSame(2, $metrics['finalizados']);
        $this->assertSame(1, $metrics['timeout']);
        $this->assertSame(1, $metrics['activos']);
        $this->assertSame('11m', $metrics['promedio']);
        $this->assertSame(660.0, $metrics['promedio_sec']);

        $this->assertCount(2, $top5);
        $this->assertSame([$equipoRapido->id, $equipoConPenalizacion->id], $top5->pluck('id')->all());
    }

    public function test_bottlenecks_are_calculated_from_phase_deltas_and_sorted_descending(): void
    {
        $partidaA = $this->createPartida();
        $partidaB = $this->createPartida([
            'email' => 'b@estudiante.xtart.com',
        ]);

        HitoFase::create([
            'partida_id' => $partidaA->id,
            'fase' => 'Prueba 1 Mario Superada',
            'segundos_transcurridos' => 100,
            'alcanzado_at' => now(),
        ]);
        HitoFase::create([
            'partida_id' => $partidaA->id,
            'fase' => 'Prueba 2 Yoseth Superada',
            'segundos_transcurridos' => 280,
            'alcanzado_at' => now(),
        ]);
        HitoFase::create([
            'partida_id' => $partidaB->id,
            'fase' => 'Prueba 1 Mario Superada',
            'segundos_transcurridos' => 120,
            'alcanzado_at' => now(),
        ]);
        HitoFase::create([
            'partida_id' => $partidaB->id,
            'fase' => 'Prueba 2 Yoseth Superada',
            'segundos_transcurridos' => 320,
            'alcanzado_at' => now(),
        ]);

        $service = app(AdminStatsService::class);
        $bottlenecks = $service->getBottlenecks();

        $this->assertCount(2, $bottlenecks);
        $this->assertSame('Prueba 2 Yoseth', $bottlenecks[0]['fase']);
        $this->assertSame(190, $bottlenecks[0]['media_segundos']);
        $this->assertSame('Prueba 1 Mario', $bottlenecks[1]['fase']);
        $this->assertSame(110, $bottlenecks[1]['media_segundos']);
    }
}
