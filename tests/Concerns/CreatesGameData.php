<?php

namespace Tests\Concerns;

use App\Models\Inscripcion;
use App\Models\Partida;
use Illuminate\Support\Str;

trait CreatesGameData
{
    protected function createPartida(array $attributes = []): Partida
    {
        return Partida::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'email' => 'equipo-'.Str::lower(Str::random(8)).'@estudiante.xtart.com',
            'nombre_equipo' => 'Equipo '.Str::upper(Str::random(4)),
            'clase' => 'DAW 2A',
            'fase_actual' => config('fases.fases.prueba1.db'),
            'tiempo_segundos' => 0,
            'inicio_juego' => now()->subMinutes(5),
            'penalizacion_segundos' => 0,
            'tiempo_pausado_segundos' => 0,
            'ultima_pausa_at' => null,
            'estado' => 'jugando',
            'ranking_posicion_bloqueada' => null,
            'logros' => [],
            'combinacion_codice' => [1, 2, 3, 4],
            'intentos_codice' => 0,
            'intentos_prueba9' => 0,
            'hints_used' => false,
            'color_hex' => '#00ff88',
            'icono' => 'ph-user-focus',
        ], $attributes));
    }

    protected function createInscripcion(array $attributes = []): Inscripcion
    {
        return Inscripcion::create(array_merge([
            'nombre_equipo' => 'Equipo '.Str::upper(Str::random(4)),
            'correo' => 'equipo-'.Str::lower(Str::random(8)).'@estudiante.xtart.com',
            'clase' => 'DAW 2A',
        ], $attributes));
    }

    protected function gameSessionFor(Partida $partida, array $extra = []): array
    {
        return array_merge([
            'email' => $partida->email,
            'nombre_equipo' => $partida->nombre_equipo,
            'clase' => $partida->clase,
        ], $extra);
    }

    protected function progressFlagsThrough(int $completedPrueba): array
    {
        $flags = ['sergio_intro_visto' => true];

        if ($completedPrueba >= 1) {
            $flags['prueba1mario_superada'] = true;
        }
        if ($completedPrueba >= 2) {
            $flags['prueba2yoseth_superada'] = true;
        }
        if ($completedPrueba >= 3) {
            $flags['prueba3angel_superada'] = true;
        }
        if ($completedPrueba >= 4) {
            $flags['prueba4arturo_superada'] = true;
        }
        if ($completedPrueba >= 5) {
            $flags['prueba5eloy_superada'] = true;
        }
        if ($completedPrueba >= 6) {
            $flags['prueba6migue_superada'] = true;
        }
        if ($completedPrueba >= 7) {
            $flags['prueba7_superada'] = true;
        }
        if ($completedPrueba >= 8) {
            $flags['prueba8_superada'] = true;
        }
        if ($completedPrueba >= 9) {
            $flags['prueba9_superada'] = true;
        }

        return $flags;
    }
}
