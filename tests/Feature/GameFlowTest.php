<?php

namespace Tests\Feature;

use App\Models\Inscripcion;
use App\Models\Partida;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_start_a_game(): void
    {
        $inscripcion = Inscripcion::create([
            'nombre_equipo' => 'Test Team',
            'correo' => 'migue@estudiante.xtart.com',
            'clase' => '1DAW',
        ]);

        $response = $this->post('/empezar', [
            'correo' => 'migue@estudiante.xtart.com',
            'codigo_acceso' => $inscripcion->codigo_acceso,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('prueba1'));

        $this->assertDatabaseHas('partidas', [
            'email' => 'migue@estudiante.xtart.com',
            'nombre_equipo' => 'Test Team',
        ]);
    }

    public function test_duplicate_email_reconnects(): void
    {
        $inscripcion = Inscripcion::create([
            'nombre_equipo' => 'Original',
            'correo' => 'repetido@estudiante.xtart.com',
            'clase' => '1DAW',
        ]);

        Partida::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'email' => 'repetido@estudiante.xtart.com',
            'nombre_equipo' => 'Original',
            'clase' => '1DAW',
            'fase_actual' => config('fases.fases.prueba1.db'),
            'tiempo_segundos' => 0,
            'inicio_juego' => now(),
            'combinacion_codice' => [0, 1, 2, 3],
            'intentos_codice' => 0,
        ]);

        $response = $this->post('/empezar', [
            'correo' => 'repetido@estudiante.xtart.com',
            'codigo_acceso' => $inscripcion->codigo_acceso,
        ]);

        // Should reconnect (redirect to prueba), not reject
        $response->assertStatus(302);
    }

    public function test_reconnect_restores_progress_for_prueba9(): void
    {
        $inscripcion = Inscripcion::create([
            'nombre_equipo' => 'Equipo Prueba 9',
            'correo' => 'prueba9@estudiante.xtart.com',
            'clase' => '2DAW',
        ]);

        Partida::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'email' => 'prueba9@estudiante.xtart.com',
            'nombre_equipo' => 'Equipo Prueba 9',
            'clase' => '2DAW',
            'fase_actual' => config('fases.fases.prueba9.db'),
            'tiempo_segundos' => 0,
            'inicio_juego' => now(),
            'combinacion_codice' => [0, 1, 2, 3],
            'intentos_codice' => 0,
        ]);

        $response = $this->post('/empezar', [
            'correo' => 'prueba9@estudiante.xtart.com',
            'codigo_acceso' => $inscripcion->codigo_acceso,
        ]);

        $response->assertRedirect(route('prueba9'));
        $response->assertSessionHas('prueba8_superada', true);
        $response->assertSessionMissing('prueba9_superada');
    }

    public function test_reconnect_restores_progress_for_finalized_games(): void
    {
        $inscripcion = Inscripcion::create([
            'nombre_equipo' => 'Equipo Final',
            'correo' => 'final@estudiante.xtart.com',
            'clase' => '2DAW',
        ]);

        Partida::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'email' => 'final@estudiante.xtart.com',
            'nombre_equipo' => 'Equipo Final',
            'clase' => '2DAW',
            'fase_actual' => config('fases.fases.finalizado.db'),
            'tiempo_segundos' => 1234,
            'inicio_juego' => now()->subMinutes(20),
            'combinacion_codice' => [4, 5, 6, 7],
            'intentos_codice' => 0,
        ]);

        $response = $this->post('/empezar', [
            'correo' => 'final@estudiante.xtart.com',
            'codigo_acceso' => $inscripcion->codigo_acceso,
        ]);

        $response->assertRedirect(route('final'));
        $response->assertSessionHas('prueba9_superada', true);
    }

    public function test_final_route_is_accessible_when_timeout_is_already_persisted(): void
    {
        Partida::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'email' => 'timeout@estudiante.xtart.com',
            'nombre_equipo' => 'Equipo Timeout',
            'clase' => '2DAW',
            'fase_actual' => config('fases.fases.timeout.db'),
            'tiempo_segundos' => 5400,
            'inicio_juego' => now()->subMinutes(90),
            'combinacion_codice' => [7, 8, 9, 0],
            'intentos_codice' => 0,
        ]);

        $response = $this->withSession([
            'email' => 'timeout@estudiante.xtart.com',
            'nombre_equipo' => 'Equipo Timeout',
            'clase' => '2DAW',
        ])->get('/final');

        $response->assertOk();
        $response->assertSessionHas('prueba9_superada', true);
        $response->assertSee('Equipo Timeout');
    }

    public function test_invalid_code_cannot_start_game(): void
    {
        Inscripcion::create([
            'nombre_equipo' => 'Test Team',
            'correo' => 'test@estudiante.xtart.com',
            'clase' => '1DAW',
        ]);

        $response = $this->post('/empezar', [
            'correo' => 'test@estudiante.xtart.com',
            'codigo_acceso' => 'XXXXXX',
        ]);

        // Should fail validation and redirect back
        $response->assertStatus(302);
        $response->assertRedirect('/');
    }
}
