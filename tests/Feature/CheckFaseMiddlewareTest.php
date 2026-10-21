<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesGameData;
use Tests\TestCase;

class CheckFaseMiddlewareTest extends TestCase
{
    use CreatesGameData;
    use RefreshDatabase;

    public function test_guest_get_requests_are_redirected_to_inicio_with_original_destination(): void
    {
        $response = $this->get('/prueba1?foo=bar');

        $response->assertRedirect(route('inicio', ['redirect_to' => '/prueba1?foo=bar'], false));
        $response->assertSessionHas('error', 'Debes registrar tu equipo antes de jugar.');
    }

    public function test_guest_post_requests_receive_a_json_forbidden_response(): void
    {
        $response = $this->postJson('/pista', [
            'prueba_numero' => 1,
        ]);

        $response->assertForbidden();
        $response->assertJson([
            'success' => false,
            'message' => 'Sesión no activa. Registra tu equipo primero.',
        ]);
    }

    public function test_progress_flags_are_restored_from_the_persisted_phase(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba4.db'),
        ]);

        $response = $this
            ->withSession($this->gameSessionFor($partida))
            ->get('/prueba4');

        $response->assertOk();
        $response->assertSessionHas('prueba1mario_superada', true);
        $response->assertSessionHas('prueba2yoseth_superada', true);
        $response->assertSessionHas('prueba3angel_superada', true);
        $response->assertSessionMissing('prueba4arturo_superada');
    }

    public function test_skipping_a_phase_redirects_to_the_latest_unlocked_route(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba4.db'),
        ]);

        $response = $this
            ->withSession($this->gameSessionFor($partida))
            ->get('/prueba5');

        $response->assertRedirect('/prueba4');
        $response->assertSessionHas('error', 'No puedes saltarte pruebas. Completa las anteriores primero.');
    }
}
