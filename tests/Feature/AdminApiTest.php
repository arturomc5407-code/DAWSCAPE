<?php

namespace Tests\Feature;

use App\Events\AdminDashboardUpdated;
use App\Events\TiempoAjustado;
use App\Models\Inscripcion;
use App\Models\LogJuego;
use App\Models\PistaUsada;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\Concerns\CreatesGameData;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use CreatesGameData;
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create();
    }

    public function test_admin_api_stats_returns_metrics_chart_and_partidas_payload(): void
    {
        $user = $this->adminUser();

        $finalizada = $this->createPartida([
            'nombre_equipo' => 'Finalizada',
            'email' => 'finalizada@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'tiempo_segundos' => 650,
            'logros' => ['speedrunner_ninja'],
            'combinacion_codice' => [7, 1, 2, 4],
        ]);

        $activa = $this->createPartida([
            'nombre_equipo' => 'Activa',
            'email' => 'activa@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.prueba5.db'),
            'tiempo_segundos' => 0,
            'inicio_juego' => now()->subMinutes(8),
            'penalizacion_segundos' => 120,
            'combinacion_codice' => [4, 3, 2, 1],
        ]);

        PistaUsada::create([
            'partida_id' => $activa->id,
            'prueba_numero' => 3,
        ]);
        PistaUsada::create([
            'partida_id' => $activa->id,
            'prueba_numero' => 5,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/admin/api/stats');

        $response->assertOk();
        $response->assertJsonPath('metrics.total', 2);
        $response->assertJsonPath('metrics.finalizados', 1);
        $response->assertJsonPath('metrics.activos', 1);
        $response->assertJsonCount(2, 'partidas');
        $response->assertJsonFragment([
            'nombre_equipo' => 'Activa',
            'fase_actual' => config('fases.fases.prueba5.db'),
            'pistas_count' => 2,
            'codice' => '4-3-2-1',
        ]);
        $response->assertJsonFragment([
            'nombre_equipo' => 'Finalizada',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'codice' => '7-1-2-4',
        ]);
    }

    public function test_admin_api_inscripciones_reports_started_and_pending_teams(): void
    {
        $user = $this->adminUser();

        $pending = Inscripcion::create([
            'nombre_equipo' => 'Pendiente',
            'correo' => 'pendiente@estudiante.xtart.com',
            'clase' => 'DAW 2A',
        ]);

        $started = Inscripcion::create([
            'nombre_equipo' => 'Iniciado',
            'correo' => 'iniciado@estudiante.xtart.com',
            'clase' => 'DAW 2A',
        ]);

        $this->createPartida([
            'email' => $started->correo,
            'nombre_equipo' => $started->nombre_equipo,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/admin/api/inscripciones');

        $response->assertOk();
        $response->assertJsonPath('total_inscritos', 2);
        $response->assertJsonPath('no_iniciados', 1);
        $response->assertJsonPath('iniciados', 1);
        $response->assertJsonCount(1, 'inscripciones_pendientes');
        $response->assertJsonFragment([
            'id' => $pending->id,
            'nombre_equipo' => 'Pendiente',
            'correo' => 'pendiente@estudiante.xtart.com',
            'clase' => 'DAW 2A',
            'avatar' => 'P',
        ]);
    }

    public function test_admin_api_equipos_activos_excludes_finished_games_and_returns_elapsed_time(): void
    {
        $user = $this->adminUser();

        $jugando = $this->createPartida([
            'nombre_equipo' => 'Jugando',
            'email' => 'jugando@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.prueba4.db'),
            'inicio_juego' => now()->subMinutes(10),
            'penalizacion_segundos' => 30,
        ]);

        $pausado = $this->createPartida([
            'nombre_equipo' => 'Pausado',
            'email' => 'pausado@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.prueba5.db'),
            'inicio_juego' => now()->subMinutes(20),
            'estado' => 'pausado',
            'tiempo_pausado_segundos' => 120,
            'ultima_pausa_at' => now()->subMinute(),
            'penalizacion_segundos' => 15,
        ]);

        $this->createPartida([
            'nombre_equipo' => 'Final',
            'email' => 'final@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'tiempo_segundos' => 1000,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/admin/api/equipos-activos');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonFragment([
            'uuid' => $jugando->uuid,
            'nombre_equipo' => 'Jugando',
            'fase_actual' => config('fases.fases.prueba4.db'),
            'estado' => 'jugando',
            'penalizacion_segundos' => 30,
        ]);
        $response->assertJsonFragment([
            'uuid' => $pausado->uuid,
            'nombre_equipo' => 'Pausado',
            'fase_actual' => config('fases.fases.prueba5.db'),
            'estado' => 'pausado',
            'penalizacion_segundos' => 15,
        ]);

        $payload = collect($response->json())->keyBy('nombre_equipo');

        $this->assertSame(630, $payload['Jugando']['tiempo_elapsed']);
        $this->assertSame(1035, $payload['Pausado']['tiempo_elapsed']);
    }

    public function test_admin_can_adjust_team_time_and_events_are_dispatched(): void
    {
        Event::fake([TiempoAjustado::class, AdminDashboardUpdated::class]);

        $user = $this->adminUser();
        $partida = $this->createPartida([
            'nombre_equipo' => 'Equipo Ajustado',
            'penalizacion_segundos' => 90,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/admin/live/ajustar-tiempo', [
                'uuid' => $partida->uuid,
                'segundos' => -30,
            ]);

        $response->assertOk();
        $response->assertJson([
            'ok' => true,
            'penalizacion_total' => 60,
            'equipo' => 'Equipo Ajustado',
        ]);

        $partida->refresh();

        $this->assertSame(60, $partida->penalizacion_segundos);
        $this->assertDatabaseHas('log_juegos', [
            'partida_id' => $partida->id,
            'accion' => 'BONUS_MANUAL',
        ]);

        $log = LogJuego::where('partida_id', $partida->id)->latest('id')->first();
        $this->assertSame(-30, $log->detalles['segundos']);
        $this->assertSame($user->name, $log->detalles['admin']);
        $this->assertSame(60, $log->detalles['penalizacion_total']);

        Event::assertDispatched(TiempoAjustado::class, function (TiempoAjustado $event) use ($partida) {
            return $event->uuid === $partida->uuid
                && $event->segundos === -30
                && $event->tipo === 'bonus'
                && $event->penalizacion_total === 60;
        });

        Event::assertDispatched(AdminDashboardUpdated::class);
    }
}
