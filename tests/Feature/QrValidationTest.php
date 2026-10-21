<?php

namespace Tests\Feature;

use App\Models\Qr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\Concerns\CreatesGameData;
use Tests\TestCase;

class QrValidationTest extends TestCase
{
    use CreatesGameData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_qr_validation_requires_the_previous_phase_to_be_completed(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba2.db'),
        ]);

        $response = $this
            ->withSession($this->gameSessionFor($partida))
            ->postJson('/validar-codigo-qr', [
                'codigo' => 'ACCESO_FASE_3',
                'prueba' => 3,
            ]);

        $response->assertForbidden();
        $response->assertJson([
            'success' => false,
            'message' => 'No tienes acceso a esta prueba todavía. Completa la prueba anterior.',
        ]);
    }

    public function test_legacy_qr_codes_are_accepted_and_mark_the_phase_as_completed(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba3.db'),
        ]);

        $response = $this
            ->withSession(array_merge($this->gameSessionFor($partida), $this->progressFlagsThrough(2)))
            ->postJson('/validar-codigo-qr', [
                'codigo' => "  acceso_fase_3\r\n",
                'prueba' => 3,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'redirect_url' => route('juego.maquina-cafe'),
        ]);
        $response->assertSessionHas('prueba_3_completada', true);
    }

    public function test_dynamic_qr_urls_are_matched_by_their_slug(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba3.db'),
        ]);

        Qr::create([
            'nombre' => 'QR Cafe',
            'descripcion' => 'Prueba dinamica',
            'fase_requerida' => 'Prueba 3 Angel',
            'url' => 'https://dawscape.test/qr/prueba3angel',
        ]);

        $response = $this
            ->withSession(array_merge($this->gameSessionFor($partida), $this->progressFlagsThrough(2)))
            ->postJson('/validar-codigo-qr', [
                'codigo' => 'https://movil.example/qr/prueba3angel',
                'prueba' => 3,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'redirect_url' => route('juego.maquina-cafe'),
        ]);
    }

    public function test_qr_validation_rejects_codes_belonging_to_another_phase(): void
    {
        $partida = $this->createPartida([
            'fase_actual' => config('fases.fases.prueba3.db'),
        ]);

        Qr::create([
            'nombre' => 'QR Snacks',
            'descripcion' => 'QR de otra fase',
            'fase_requerida' => 'Prueba 4 Arturo',
            'url' => 'https://dawscape.test/qr/prueba4arturo',
        ]);

        $response = $this
            ->withSession(array_merge($this->gameSessionFor($partida), $this->progressFlagsThrough(2)))
            ->postJson('/validar-codigo-qr', [
                'codigo' => 'https://dawscape.test/qr/prueba4arturo',
                'prueba' => 3,
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Este código QR pertenece a otra prueba.',
        ]);
    }
}
