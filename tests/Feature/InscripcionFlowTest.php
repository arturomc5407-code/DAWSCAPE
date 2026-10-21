<?php

namespace Tests\Feature;

use App\Mail\CodigoAccesoInscripcion;
use App\Models\Inscripcion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InscripcionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_team_can_register_and_the_access_email_is_sent(): void
    {
        Mail::fake();

        $response = $this->post('/inscripcion', [
            'nombre_equipo' => 'Equipo Test',
            'correo' => 'equipo.test@estudiante.xtart.com',
            'ciclo' => 'DAM',
            'grupo' => '2ºA',
            'acepta_terminos' => '1',
        ]);

        $inscripcion = Inscripcion::where('correo', 'equipo.test@estudiante.xtart.com')->firstOrFail();

        $response->assertRedirect(route('inscripcion.exito', ['token' => $inscripcion->qr_token], false));
        $response->assertSessionHas('correo_enviado', true);
        $response->assertSessionMissing('correo_fallido_codigo');

        $this->assertSame('DAM 2ºA', $inscripcion->clase);
        $this->assertSame(6, strlen($inscripcion->codigo_acceso));
        $this->assertNotEmpty($inscripcion->qr_token);

        Mail::assertSent(CodigoAccesoInscripcion::class, function (CodigoAccesoInscripcion $mail) use ($inscripcion) {
            return $mail->hasTo($inscripcion->correo) && $mail->inscripcion->is($inscripcion);
        });
    }

    public function test_registration_rejects_groups_that_do_not_belong_to_the_selected_cycle(): void
    {
        $response = $this->from('/inscripcion')->post('/inscripcion', [
            'nombre_equipo' => 'Equipo Invalido',
            'correo' => 'equipo.invalido@estudiante.xtart.com',
            'ciclo' => 'DAW',
            'grupo' => '1ºB',
            'acepta_terminos' => '1',
        ]);

        $response->assertRedirect('/inscripcion');
        $response->assertSessionHasErrors(['grupo']);
        $this->assertDatabaseMissing('inscripcions', [
            'correo' => 'equipo.invalido@estudiante.xtart.com',
        ]);
    }

    public function test_a_registration_qr_token_can_be_validated(): void
    {
        $inscripcion = Inscripcion::create([
            'nombre_equipo' => 'Equipo QR',
            'correo' => 'equipo.qr@estudiante.xtart.com',
            'clase' => 'DAW 2A',
        ]);

        $response = $this->getJson('/inscripcion/validar/'.$inscripcion->qr_token);

        $response->assertOk();
        $response->assertJson([
            'valido' => true,
            'equipo' => 'Equipo QR',
            'correo' => 'equipo.qr@estudiante.xtart.com',
            'clase' => 'DAW 2A',
        ]);
    }
}
