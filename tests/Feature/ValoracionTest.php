<?php

namespace Tests\Feature;

use App\Models\Valoracion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesGameData;
use Tests\TestCase;

class ValoracionTest extends TestCase
{
    use CreatesGameData;
    use RefreshDatabase;

    public function test_a_team_can_submit_a_rating(): void
    {
        $partida = $this->createPartida();

        $response = $this->postJson('/valoraciones', [
            'partida_id' => $partida->id,
            'estrellas' => 5,
            'comentario' => 'Muy divertido',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('valoraciones', [
            'partida_id' => $partida->id,
            'nombre_equipo' => $partida->nombre_equipo,
            'estrellas' => 5,
            'comentario' => 'Muy divertido',
        ]);
    }

    public function test_a_team_cannot_submit_two_ratings_for_the_same_game(): void
    {
        $partida = $this->createPartida();

        Valoracion::create([
            'partida_id' => $partida->id,
            'nombre_equipo' => $partida->nombre_equipo,
            'estrellas' => 4,
            'comentario' => 'Primera valoracion',
        ]);

        $response = $this->postJson('/valoraciones', [
            'partida_id' => $partida->id,
            'estrellas' => 5,
            'comentario' => 'Intento duplicado',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Ya valorado',
        ]);

        $this->assertDatabaseCount('valoraciones', 1);
    }
}
