<?php

namespace Tests\Feature;

use App\Models\PreguntaDinamica;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\Concerns\CreatesGameData;
use Tests\TestCase;

class AdvancedGameFlowTest extends TestCase
{
    use CreatesGameData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_correct_codice_combination_advances_to_prueba8(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba7.db'),
            'combinacion_codice' => [4, 2, 7, 9],
        ]);

        $response = $this
            ->withSession(array_merge($this->gameSessionFor($partida), $this->progressFlagsThrough(6)))
            ->postJson('/prueba7', [
                'digits' => [4, 2, 7, 9],
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertSessionHas('prueba7_superada', true);

        $partida->refresh();

        $this->assertSame(config('fases.fases.prueba8.db'), $partida->fase_actual);
        $this->assertDatabaseHas('hitos_fases', [
            'partida_id' => $partida->id,
            'fase' => 'Prueba 7 Dinamica Superada',
        ]);
    }

    public function test_wrong_codice_attempt_increments_the_attempt_counter(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba7.db'),
            'combinacion_codice' => [1, 2, 3, 4],
        ]);

        $response = $this
            ->withSession(array_merge($this->gameSessionFor($partida), $this->progressFlagsThrough(6)))
            ->postJson('/prueba7', [
                'digits' => [9, 9, 9, 9],
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => false,
            'intentos' => 1,
        ]);

        $partida->refresh();

        $this->assertSame(1, $partida->intentos_codice);
        $this->assertSame(config('fases.fases.prueba7.db'), $partida->fase_actual);
    }

    public function test_prueba8_accepts_alternative_answers_and_advances_the_phase(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba8.db'),
        ]);

        $preguntas = collect([
            [
                'enunciado' => 'Capital',
                'respuesta_correcta' => 'Madrid',
                'respuestas_alternativas' => ['villa y corte'],
                'activa' => true,
            ],
            [
                'enunciado' => 'Proceso',
                'respuesta_correcta' => 'Evaporacion, Evaporación',
                'respuestas_alternativas' => [],
                'activa' => true,
            ],
            [
                'enunciado' => 'Web',
                'respuesta_correcta' => 'HTML',
                'respuestas_alternativas' => [],
                'activa' => true,
            ],
            [
                'enunciado' => 'Heroe',
                'respuesta_correcta' => 'Batman',
                'respuestas_alternativas' => [],
                'activa' => true,
            ],
            [
                'enunciado' => 'Minutos',
                'respuesta_correcta' => '90',
                'respuestas_alternativas' => [],
                'activa' => true,
            ],
        ])->map(fn (array $data) => PreguntaDinamica::create($data));

        $response = $this
            ->withSession(array_merge(
                $this->gameSessionFor($partida),
                $this->progressFlagsThrough(7),
                ['prueba8_preguntas' => $preguntas->pluck('id')->all()]
            ))
            ->postJson('/prueba8', [
                'completed' => true,
                'respuestas_usuario' => [
                    'villa y corte',
                    'evaporacion',
                    'html',
                    'batman',
                    '90',
                ],
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertSessionHas('prueba8_superada', true);
        $response->assertSessionMissing('prueba8_preguntas');

        $partida->refresh();

        $this->assertSame(config('fases.fases.prueba9.db'), $partida->fase_actual);
        $this->assertDatabaseHas('hitos_fases', [
            'partida_id' => $partida->id,
            'fase' => 'Prueba 8 Cultura General Superada',
        ]);
    }

    public function test_prueba8_is_idempotent_when_the_database_is_already_beyond_that_phase(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba9.db'),
        ]);

        $response = $this
            ->withSession(array_merge(
                $this->gameSessionFor($partida),
                $this->progressFlagsThrough(7),
                ['prueba8_preguntas' => [10, 20, 30, 40, 50]]
            ))
            ->postJson('/prueba8', [
                'completed' => false,
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertSessionHas('prueba8_superada', true);
        $response->assertSessionMissing('prueba8_preguntas');
    }

    public function test_prueba9_correct_answers_advance_the_question_index(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba9.db'),
        ]);

        $response = $this
            ->withSession(array_merge($this->gameSessionFor($partida), $this->progressFlagsThrough(8)))
            ->postJson('/prueba9', [
                'action' => 'answer',
                'answer' => '  EDUARDO  ',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'state' => 'question',
            'letter' => 'N',
            'nextIndex' => 1,
            'total' => 5,
        ]);
        $response->assertSessionHas('prueba9_index', 1);
        $response->assertSessionHas('prueba9_letters', ['N']);
    }

    public function test_prueba9_cannot_be_completed_by_submitting_the_password_early(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba9.db'),
        ]);

        $response = $this
            ->withSession(array_merge($this->gameSessionFor($partida), $this->progressFlagsThrough(8)))
            ->postJson('/prueba9', [
                'action' => 'password',
                'password' => 'nacho',
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Debes completar todas las preguntas antes de introducir la contraseña.',
        ]);

        $partida->refresh();

        $this->assertSame(config('fases.fases.prueba9.db'), $partida->fase_actual);
        $this->assertFalse((bool) session('prueba9_superada', false));
    }

    public function test_prueba9_marks_hints_as_used_on_the_fifth_failed_attempt(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba9.db'),
            'intentos_prueba9' => 4,
        ]);

        $response = $this
            ->withSession(array_merge(
                $this->gameSessionFor($partida),
                $this->progressFlagsThrough(8),
                ['prueba9_intentos_fallidos' => 4]
            ))
            ->postJson('/prueba9', [
                'action' => 'answer',
                'answer' => 'incorrecta',
            ]);

        $response->assertOk();
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('fails', 5);

        $partida->refresh();

        $this->assertSame(5, $partida->intentos_prueba9);
        $this->assertTrue((bool) $partida->hints_used);
        $this->assertNotNull($response->json('hint'));
    }

    public function test_prueba9_finishes_the_game_once_all_letters_and_password_are_correct(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba9.db'),
        ]);

        $response = $this
            ->withSession(array_merge(
                $this->gameSessionFor($partida),
                $this->progressFlagsThrough(8),
                [
                    'prueba9_index' => 5,
                    'prueba9_letters' => ['N', 'A', 'C', 'H', 'O'],
                    'prueba9_intentos_fallidos' => 2,
                ]
            ))
            ->postJson('/prueba9', [
                'action' => 'password',
                'password' => 'nacho',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'state' => 'done',
        ]);
        $response->assertSessionHas('prueba9_superada', true);
        $response->assertSessionMissing('prueba9_index');
        $response->assertSessionMissing('prueba9_letters');
        $response->assertSessionMissing('prueba9_intentos_fallidos');

        $partida->refresh();

        $this->assertSame(config('fases.fases.finalizado.db'), $partida->fase_actual);
        $this->assertGreaterThan(0, $partida->tiempo_segundos);
        $this->assertDatabaseHas('hitos_fases', [
            'partida_id' => $partida->id,
            'fase' => 'Prueba 9 Juego de Pistas Superada',
        ]);
    }
}
