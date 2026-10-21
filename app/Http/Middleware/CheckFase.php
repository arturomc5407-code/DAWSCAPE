<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Partida;
use Illuminate\Http\Request;

class CheckFase
{
    /**
     * Progresión lineal: cada prueba requiere que se haya superado la anterior.
     * La clave es el segmento de la URL (ej: 'prueba3'), el valor es la
     * flag de sesión que debe existir para poder acceder.
     */
    private const REQUISITOS = [
        'prueba2'  => 'prueba1mario_superada',
        'prueba3'  => 'prueba2yoseth_superada',
        'prueba4'  => 'prueba3angel_superada',
        'prueba5'  => 'prueba4arturo_superada',
        'prueba6'  => 'prueba5eloy_superada',
        'prueba7'  => 'prueba6migue_superada',
        'prueba8'  => 'prueba7_superada',
        'prueba9'  => 'prueba8_superada',
        'final'    => 'prueba9_superada',
    ];

    /**
     * Verifica que el jugador tiene una sesión activa y que no se salta fases.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('email')) {
            if ($request->wantsJson() || $request->isMethod('post')) {
                return response()->json(['success' => false, 'message' => 'Sesión no activa. Registra tu equipo primero.'], 403);
            }
            $destino = '/' . ltrim($request->path(), '/');
            $qs = $request->getQueryString();
            if (!empty($qs)) {
                $destino .= '?' . $qs;
            }

            return redirect()
                ->route('inicio', ['redirect_to' => $destino])
                ->with('error', 'Debes registrar tu equipo antes de jugar.');
        }

        $this->sincronizarProgresoDesdeBaseDeDatos();

        // Comprobar progresión lineal (solo en GETs de pruebas)
        if ($request->isMethod('get')) {
            $segmento = $request->segment(1); // prueba1, prueba2, ..., final

            if (isset(self::REQUISITOS[$segmento]) && !session(self::REQUISITOS[$segmento])) {
                // Determinar a qué prueba debería estar el jugador
                $rutaCorrecta = $this->rutaActual();

                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'No puedes saltarte pruebas.'], 403);
                }

                return redirect($rutaCorrecta)
                    ->with('error', 'No puedes saltarte pruebas. Completa las anteriores primero.');
            }
        }

        return $next($request);
    }

    /**
     * Si la sesión pierde flags, las recompone desde la fase persistida.
     */
    private function sincronizarProgresoDesdeBaseDeDatos(): void
    {
        $email = session('email');
        if (!$email) {
            return;
        }

        $partida = Partida::where('email', $email)->first();
        if (!$partida) {
            return;
        }

        $nivelesPorFase = [
            config('fases.fases.prueba1.db')    => 1,
            config('fases.fases.prueba2.db')    => 2,
            config('fases.fases.prueba3.db')    => 3,
            config('fases.fases.prueba4.db')    => 4,
            config('fases.fases.prueba5.db')    => 5,
            config('fases.fases.prueba6.db')    => 6,
            config('fases.fases.prueba7.db')    => 7,
            config('fases.fases.prueba8.db')    => 8,
            config('fases.fases.prueba9.db')    => 9,
            config('fases.fases.finalizado.db') => 10,
            config('fases.fases.timeout.db')    => 10,
        ];

        $nivelActual = $nivelesPorFase[trim((string) $partida->fase_actual)] ?? 0;
        if ($nivelActual <= 0) {
            return;
        }

        $flags = [];
        if ($nivelActual >= 2) $flags['prueba1mario_superada'] = true;
        if ($nivelActual >= 3) $flags['prueba2yoseth_superada'] = true;
        if ($nivelActual >= 4) $flags['prueba3angel_superada'] = true;
        if ($nivelActual >= 5) $flags['prueba4arturo_superada'] = true;
        if ($nivelActual >= 6) $flags['prueba5eloy_superada'] = true;
        if ($nivelActual >= 7) $flags['prueba6migue_superada'] = true;
        if ($nivelActual >= 8) $flags['prueba7_superada'] = true;
        if ($nivelActual >= 9) $flags['prueba8_superada'] = true;
        if ($nivelActual >= 10) $flags['prueba9_superada'] = true;

        if (!empty($flags)) {
            session($flags);
        }
    }

    /**
     * Devuelve la ruta de la prueba más avanzada que el jugador puede ver.
     */
    private function rutaActual(): string
    {
        $orden = ['prueba1', 'prueba2', 'prueba3', 'prueba4', 'prueba5', 'prueba6', 'prueba7', 'prueba8', 'prueba9', 'final'];

        foreach ($orden as $ruta) {
            if (isset(self::REQUISITOS[$ruta]) && !session(self::REQUISITOS[$ruta])) {
                // La anterior es la última desbloqueada
                $idx = array_search($ruta, $orden);
                return '/' . ($idx > 0 ? $orden[$idx - 1] : $orden[0]);
            }
        }

        return '/final';
    }
}
