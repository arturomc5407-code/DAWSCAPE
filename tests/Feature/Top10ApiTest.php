<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\Concerns\CreatesGameData;
use Tests\TestCase;

class Top10ApiTest extends TestCase
{
    use CreatesGameData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 5, 6, 12, 0, 0));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_top10_api_separates_finished_ranking_and_live_teams(): void
    {
        Cache::put('top10_ticker', 'Ticker de pruebas');

        $this->createPartida([
            'nombre_equipo' => 'Finalizado Bloqueado',
            'email' => 'finalizado.bloqueado@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'tiempo_segundos' => 500,
            'ranking_posicion_bloqueada' => 2,
            'penalizacion_segundos' => 0,
        ]);

        $recienFinalizado = $this->createPartida([
            'nombre_equipo' => 'Recien Finalizado',
            'email' => 'recien.finalizado@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'tiempo_segundos' => 700,
            'ranking_posicion_bloqueada' => null,
            'penalizacion_segundos' => 0,
        ]);

        $timeout = $this->createPartida([
            'nombre_equipo' => 'Timeout',
            'email' => 'timeout.top10@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.timeout.db'),
            'tiempo_segundos' => 5400,
            'ranking_posicion_bloqueada' => null,
            'penalizacion_segundos' => 0,
        ]);

        $this->createPartida([
            'nombre_equipo' => 'Jugando Avanzado',
            'email' => 'jugando.avanzado@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.prueba8.db'),
            'inicio_juego' => now()->subMinutes(8),
            'penalizacion_segundos' => 0,
            'color_hex' => '#112233',
            'icono' => 'ph-rocket-launch',
            'logros' => ['cerebro_intacto'],
        ]);

        $this->createPartida([
            'nombre_equipo' => 'Jugando Basico',
            'email' => 'jugando.basico@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.prueba6.db'),
            'inicio_juego' => now()->subMinutes(6),
            'penalizacion_segundos' => 480,
        ]);

        $response = $this->getJson('/api/top10');

        $response->assertOk();
        $response->assertJsonPath('ticker', 'Ticker de pruebas');
        $response->assertJsonCount(3, 'top10');
        $response->assertJsonCount(3, 'finalizados');
        $response->assertJsonCount(2, 'en_progreso');

        $finalizados = $response->json('finalizados');
        $live = $response->json('en_progreso');

        $this->assertSame('Finalizado Bloqueado', $finalizados[0]['nombre_equipo']);
        $this->assertSame(1, $finalizados[0]['rank']);
        $this->assertSame(null, $finalizados[0]['gap']);
        $this->assertSame('finalizado', $finalizados[0]['estado']);
        $this->assertSame(2, $finalizados[0]['ranking_posicion_bloqueada']);

        $this->assertSame('Recien Finalizado', $finalizados[1]['nombre_equipo']);
        $this->assertSame(2, $finalizados[1]['rank']);
        $this->assertSame('+3m 20s sobre el 1º', $finalizados[1]['gap']);
        $this->assertSame('finalizado', $finalizados[1]['estado']);

        $this->assertSame('Timeout', $finalizados[2]['nombre_equipo']);
        $this->assertSame(3, $finalizados[2]['rank']);
        $this->assertSame(null, $finalizados[2]['gap']);
        $this->assertSame('timeout', $finalizados[2]['estado']);

        $this->assertSame('Finalizado Bloqueado', $response->json('top10.0.nombre_equipo'));

        $this->assertSame('Jugando Avanzado', $live[0]['nombre_equipo']);
        $this->assertSame(1, $live[0]['rank']);
        $this->assertSame(null, $live[0]['gap']);
        $this->assertSame('jugando', $live[0]['estado']);
        $this->assertSame('#112233', $live[0]['color_hex']);
        $this->assertSame('ph-rocket-launch', $live[0]['icono']);
        $this->assertSame(['cerebro_intacto'], $live[0]['logros']);
        $this->assertSame(0, $live[0]['pistas_usadas']);
        $this->assertSame(89, $live[0]['progreso_porcentaje']);

        $this->assertSame('Jugando Basico', $live[1]['nombre_equipo']);
        $this->assertSame(2, $live[1]['rank']);
        $this->assertSame('jugando', $live[1]['estado']);

        $recienFinalizado->refresh();
        $timeout->refresh();

        $this->assertNull($recienFinalizado->ranking_posicion_bloqueada);
        $this->assertNull($timeout->ranking_posicion_bloqueada);
    }

    public function test_top10_api_keeps_live_column_filled_with_best_10_active_teams(): void
    {
        $this->createPartida([
            'nombre_equipo' => 'Finalizado 01',
            'email' => 'finalizado01@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'tiempo_segundos' => 650,
        ]);

        $this->createPartida([
            'nombre_equipo' => 'Finalizado 02',
            'email' => 'finalizado02@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.timeout.db'),
            'tiempo_segundos' => 5400,
        ]);

        for ($i = 1; $i <= 12; $i++) {
            $this->createPartida([
                'nombre_equipo' => 'Activo ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'email' => 'activo' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '@estudiante.xtart.com',
                'fase_actual' => config('fases.fases.prueba8.db'),
                'inicio_juego' => now()->subMinutes($i),
                'penalizacion_segundos' => 0,
            ]);
        }

        $response = $this->getJson('/api/top10');

        $response->assertOk();
        $response->assertJsonCount(10, 'en_progreso');
        $response->assertJsonCount(2, 'finalizados');

        $this->assertSame(
            [
                'Activo 01',
                'Activo 02',
                'Activo 03',
                'Activo 04',
                'Activo 05',
                'Activo 06',
                'Activo 07',
                'Activo 08',
                'Activo 09',
                'Activo 10',
            ],
            collect($response->json('en_progreso'))->pluck('nombre_equipo')->all()
        );
    }

    public function test_top10_api_orders_finished_ranking_by_time_without_hint_penalty(): void
    {
        $this->createPartida([
            'nombre_equipo' => 'Rapido con pistas',
            'email' => 'rapido.pistas@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'tiempo_segundos' => 1080,
            'penalizacion_segundos' => 480,
        ]);

        $this->createPartida([
            'nombre_equipo' => 'Lento sin pistas',
            'email' => 'lento.sin.pistas@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'tiempo_segundos' => 900,
            'penalizacion_segundos' => 0,
        ]);

        $response = $this->getJson('/api/top10');

        $response->assertOk();
        $response->assertJsonCount(2, 'finalizados');

        $finalizados = $response->json('finalizados');

        $this->assertSame('Rapido con pistas', $finalizados[0]['nombre_equipo']);
        $this->assertSame(600, $finalizados[0]['tiempo_segundos']);
        $this->assertSame('10m 00s', $finalizados[0]['tiempo_formateado']);
        $this->assertSame(1080, $finalizados[0]['tiempo_total_segundos']);
        $this->assertSame(1, $finalizados[0]['pistas_usadas']);

        $this->assertSame('Lento sin pistas', $finalizados[1]['nombre_equipo']);
        $this->assertSame(900, $finalizados[1]['tiempo_segundos']);
        $this->assertSame('+5m 00s sobre el 1º', $finalizados[1]['gap']);
    }

    public function test_top10_api_calculates_used_hints_for_live_teams_and_formats_base_time(): void
    {
        $partida = $this->createPartida([
            'nombre_equipo' => 'Equipo con pista',
            'email' => 'equipo.pista@estudiante.xtart.com',
            'fase_actual' => config('fases.fases.prueba5.db'),
            'inicio_juego' => now()->subMinutes(10),
            'penalizacion_segundos' => 960,
        ]);

        $response = $this->getJson('/api/top10');

        $response->assertOk();
        $response->assertJsonCount(0, 'top10');
        $response->assertJsonCount(1, 'en_progreso');
        $response->assertJsonFragment([
            'nombre_equipo' => 'Equipo con pista',
            'estado' => 'jugando',
            'pistas_usadas' => 2,
            'tiempo_formateado' => '10m 00s',
            'tiempo_total_formateado' => '26m 00s',
            'fase_numero' => 5,
            'progreso_porcentaje' => 56,
        ]);

        $this->assertSame($partida->id, $response->json('en_progreso.0.id'));
        $this->assertSame(600, $response->json('en_progreso.0.tiempo_segundos'));
        $this->assertSame(1560, $response->json('en_progreso.0.tiempo_total_segundos'));
    }
}
