<?php

namespace Tests\Feature;

use App\Models\PistaUsada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesGameData;
use Tests\TestCase;

class HintSystemTest extends TestCase
{
    use CreatesGameData;
    use RefreshDatabase;

    public function test_first_hint_use_adds_penalty_and_records_usage(): void
    {
        $partida = $this->createPartida();

        $response = $this
            ->withSession($this->gameSessionFor($partida))
            ->postJson('/pista', [
                'prueba_numero' => 3,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'ya_usada' => false,
            'penalizacion' => config('escape.penalizacion_pista'),
        ]);

        $partida->refresh();

        $this->assertSame(config('escape.penalizacion_pista'), $partida->penalizacion_segundos);
        $this->assertDatabaseHas('pistas_usadas', [
            'partida_id' => $partida->id,
            'prueba_numero' => 3,
        ]);
        $this->assertDatabaseHas('log_juegos', [
            'partida_id' => $partida->id,
            'accion' => 'Pista Usada',
        ]);
    }

    public function test_reusing_the_same_hint_does_not_add_penalty_twice(): void
    {
        $partida = $this->createPartida([
            'penalizacion_segundos' => config('escape.penalizacion_pista'),
        ]);

        PistaUsada::create([
            'partida_id' => $partida->id,
            'prueba_numero' => 3,
        ]);

        $response = $this
            ->withSession($this->gameSessionFor($partida))
            ->postJson('/pista', [
                'prueba_numero' => 3,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'ya_usada' => true,
            'penalizacion' => 0,
        ]);

        $partida->refresh();

        $this->assertSame(config('escape.penalizacion_pista'), $partida->penalizacion_segundos);
        $this->assertDatabaseCount('pistas_usadas', 1);
    }
}
