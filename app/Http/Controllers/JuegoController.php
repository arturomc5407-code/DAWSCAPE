<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Partida;
use App\Models\Inscripcion;
use App\Models\PreguntaDinamica;
use Barryvdh\DomPDF\Facade\Pdf;
use \App\Services\GameTimerService;
use App\Http\Requests\EmpezarPartidaRequest;
use App\Http\Requests\ValidarPrueba1Request;
use App\Http\Requests\ValidarPrueba3Request;
use App\Http\Requests\ValidarPrueba4Request;
use App\Http\Requests\ValidarPrueba5Request;
use App\Http\Requests\ValidarPrueba6Request;
use App\Events\Top10Updated;
use App\Events\AdminDashboardUpdated;
use App\Models\LogJuego;
use App\Models\PistaUsada;

class JuegoController extends Controller
{
    protected $timerService;

    public function __construct(GameTimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    private function logAccion($partida, $fase, $accion, $detalles = null)
    {
        if ($partida) {
            LogJuego::create([
                'partida_id' => $partida->id,
                'fase' => $fase,
                'accion' => $accion,
                'detalles' => $detalles
            ]);
        }
    }

    /**
     * Notifica al dashboard admin y top10 de cambios en tiempo real.
     * Envuelto en try-catch porque un fallo de Pusher/broadcasting
     * NUNCA debe impedir que la validación de una prueba se complete.
     */
    private function broadcastDashboard(): void
    {
        try {
            Top10Updated::dispatch();
            AdminDashboardUpdated::dispatch();
        } catch (\Throwable $e) {
            \Log::warning('Broadcasting falló (no crítico): ' . $e->getMessage());
        }
    }

    private function desbloquearLogro($partida, $logroId)
    {
        if (!$partida) return false;

        $logrosActuales = $partida->logros ?? [];
        if (!in_array($logroId, $logrosActuales)) {
            $logrosActuales[] = $logroId;
            $partida->update(['logros' => $logrosActuales]);

            $this->logAccion($partida, $partida->fase_actual, 'Logro Desbloqueado', ['logro' => $logroId]);

            $pendientes = session('nuevos_logros', []);
            $pendientes[] = $logroId;
            session(['nuevos_logros' => $pendientes]);

            return true;
        }
        return false;
    }


    /**
     * Obtiene los números de prueba donde este equipo ya usó pista.
     */
    private function obtenerPistasUsadas($partida): array
    {
        if (!$partida) return [];
        return $partida->pistasUsadas()->pluck('prueba_numero')->toArray();
    }

    /**
     * Si la partida ya superó esta prueba (su peso es menor que el actual),
     * devuelve un Redirect a la fase real del equipo. Evita que se vuelva
     * a jugar (refresco, atrás del navegador, etc.) y se duplique el hito.
     */
    private function redirigirSiYaSuperada($partida, int $pesoPrueba)
    {
        if (!$partida) return null;

        $faseActualConfig = collect(config('fases.fases'))->firstWhere('db', $partida->fase_actual);
        $pesoActual = $faseActualConfig['peso'] ?? 0;

        if ($pesoActual <= $pesoPrueba) return null;

        $rutas = [
            config('fases.fases.prueba1.db')    => 'prueba1',
            config('fases.fases.prueba2.db')    => 'prueba2',
            config('fases.fases.prueba3.db')    => 'prueba3',
            config('fases.fases.prueba4.db')    => 'prueba4',
            config('fases.fases.prueba5.db')    => 'prueba5',
            config('fases.fases.prueba6.db')    => 'prueba6',
            config('fases.fases.prueba7.db')    => 'prueba7',
            config('fases.fases.prueba8.db')    => 'prueba8',
            config('fases.fases.prueba9.db')    => 'prueba9',
            config('fases.fases.finalizado.db') => 'final',
            config('fases.fases.timeout.db')    => 'final',
        ];

        $ruta = $rutas[$partida->fase_actual] ?? null;
        return $ruta ? redirect()->route($ruta) : null;
    }

    /**
     * Rehidrata las flags de progreso en sesión usando `partidas.fase_actual`
     * como fuente de verdad al reconectar un equipo.
     */
    private function restaurarProgresoDesdeFaseActual(?string $faseActual): int
    {
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

        $nivelActual = $nivelesPorFase[trim((string) $faseActual)] ?? 0;

        session()->forget([
            'sergio_intro_visto',
            'prueba1mario_superada',
            'prueba2yoseth_superada',
            'prueba3angel_superada',
            'prueba4arturo_superada',
            'prueba5eloy_superada',
            'prueba6migue_superada',
            'prueba7_superada',
            'prueba8_superada',
            'prueba9_superada',
        ]);

        $flagsProgreso = ['sergio_intro_visto' => true];
        if ($nivelActual >= 2) $flagsProgreso['prueba1mario_superada'] = true;
        if ($nivelActual >= 3) $flagsProgreso['prueba2yoseth_superada'] = true;
        if ($nivelActual >= 4) $flagsProgreso['prueba3angel_superada'] = true;
        if ($nivelActual >= 5) $flagsProgreso['prueba4arturo_superada'] = true;
        if ($nivelActual >= 6) $flagsProgreso['prueba5eloy_superada'] = true;
        if ($nivelActual >= 7) $flagsProgreso['prueba6migue_superada'] = true;
        if ($nivelActual >= 8) $flagsProgreso['prueba7_superada'] = true;
        if ($nivelActual >= 9) $flagsProgreso['prueba8_superada'] = true;
        if ($nivelActual >= 10) $flagsProgreso['prueba9_superada'] = true;

        session($flagsProgreso);

        return $nivelActual;
    }

    // =====================================================
    // SISTEMA DE PISTAS / AYUDA
    // =====================================================
    public function usarPista(Request $request)
    {
        $request->validate([
            'prueba_numero' => 'required|integer|min:1|max:7',
        ]);

        $pruebaNumero = (int) $request->prueba_numero;
        $partida = $this->timerService->obtenerPartidaActiva();

        if (!$partida) {
            return response()->json(['success' => false, 'message' => 'No hay partida activa.'], 403);
        }

        $pistaConfig = config("escape.pistas.{$pruebaNumero}");
        if (!$pistaConfig) {
            return response()->json(['success' => false, 'message' => 'No existe pista para esta prueba.'], 404);
        }

        // ¿Ya usó esta pista?
        $yaUsada = PistaUsada::where('partida_id', $partida->id)
                             ->where('prueba_numero', $pruebaNumero)
                             ->exists();

        if ($yaUsada) {
            return response()->json([
                'success' => true,
                'ya_usada' => true,
                'texto' => $pistaConfig['texto'],
                'penalizacion' => 0,
            ]);
        }

        // PRIMERA VEZ: registrar + penalizar
        $penalizacion = config('escape.penalizacion_pista', 60);

        PistaUsada::create([
            'partida_id' => $partida->id,
            'prueba_numero' => $pruebaNumero,
        ]);

        $partida->increment('penalizacion_segundos', $penalizacion);

        $this->logAccion($partida, $partida->fase_actual, 'Pista Usada', [
            'prueba' => $pruebaNumero,
            'penalizacion' => $penalizacion,
        ]);

        return response()->json([
            'success' => true,
            'ya_usada' => false,
            'texto' => $pistaConfig['texto'],
            'penalizacion' => $penalizacion,
        ]);
    }

    // --- LOGIN DE EQUIPO ---
    public function empezarPartida(EmpezarPartidaRequest $request)
    {
        $redirDestino = $request->input('redirect_to');

        // Buscar la inscripción que coincida exactamente con los 2 datos (correo y código de acceso)
        $inscripcion = Inscripcion::where('correo', $request->correo)
            ->where('codigo_acceso', strtoupper($request->codigo_acceso))
            ->first();

        if (!$inscripcion) {
            return back()->with('error', 'Datos incorrectos o código inválido. Verifica tu correo y código de acceso.');
        }

        // ── RECONEXIÓN: el equipo ya existe, solo restauramos la sesión ────
        $partidaExistente = Partida::where('email', $inscripcion->correo)->first();
        if ($partidaExistente) {
            // Restauramos la sesión con los datos de la partida guardados en BBDD
            session([
                'email'         => $partidaExistente->email,
                'nombre_equipo' => $partidaExistente->nombre_equipo,
                'clase'         => $partidaExistente->clase,
                'inscripcion_id' => $inscripcion->id,
            ]);

            // Restaurar progreso en la sesión según la fase en BBDD
            $nivelDB = $this->restaurarProgresoDesdeFaseActual($partidaExistente->fase_actual);

            // Actualizar avatar o color si el jugador lo seleccionó en el form (opcional)
            $updateData = [];
            if ($request->filled('icono')) {
                $updateData['icono'] = $request->icono;
            }
            if ($request->filled('color_hex')) {
                $updateData['color_hex'] = $request->color_hex;
            }

            // Asegurar que si una partida antigua se reconecta sin código generado, se le genere uno aquí
            if (empty($partidaExistente->combinacion_codice)) {
                $digitos = range(0, 9);
                shuffle($digitos);
                $updateData['combinacion_codice'] = array_slice($digitos, 0, 4);
                $updateData['intentos_codice'] = 0;
            }

            if (!empty($updateData)) {
                $partidaExistente->update($updateData);
            }

            // Redirigimos al QR (o a la prueba correspondiente)
            if ($redirDestino && str_starts_with($redirDestino, '/')) {
                $destino = $redirDestino;
            } else {
                // Autocalcular destino según nivel
                $destinos = [
                    1 => route('prueba1'),
                    2 => route('prueba2'),
                    3 => route('prueba3'),
                    4 => route('prueba4'),
                    5 => route('prueba5'),
                    6 => route('prueba6'),
                    7 => route('prueba7'),
                    8 => route('prueba8'),
                    9 => route('prueba9'),
                    10 => route('final'),
                ];
                $destino = $destinos[$nivelDB] ?? route('prueba1');
            }

            return redirect($destino);
        }

        // ── REGISTRO NUEVO ────────────────────────────────────────────────
        $digitos = range(0, 9);
        shuffle($digitos);
        $combinacion = array_slice($digitos, 0, 4);

        Partida::create([
            'uuid'              => (string) \Illuminate\Support\Str::uuid(),
            'email'             => $inscripcion->correo,
            'nombre_equipo'     => $inscripcion->nombre_equipo,
            'clase'             => $inscripcion->clase,
            'fase_actual'       => config('fases.fases.prueba1.db'),
            'tiempo_segundos'   => 0,
            'inicio_juego'      => Carbon::now(),
            'combinacion_codice' => $combinacion,
            'intentos_codice'   => 0,
            'icono'             => $request->input('icono', 'ph-first-aid'),
            'color_hex'         => $request->input('color_hex', '#00ff88'),
        ]);

        session([
            'email'             => $inscripcion->correo,
            'nombre_equipo'     => $inscripcion->nombre_equipo,
            'clase'             => $inscripcion->clase,
            'inscripcion_id'    => $inscripcion->id,
        ]);

        $destino = ($redirDestino && str_starts_with($redirDestino, '/')) ? $redirDestino : route('prueba1');
        return redirect($destino);
    }

    // =====================================================
    // PRUEBA 1 MARIO (Antigua Prueba 3: AULA 115)
    // =====================================================
    public function mostrarPrueba1()
    {

        if ($this->timerService->verificarTimeout()) return redirect()->route('final')->with('timeout', true);

        $partida = $this->timerService->obtenerPartidaActiva();
        if ($redir = $this->redirigirSiYaSuperada($partida, 1)) return $redir;

        // Mostrar briefing de Sergio antes de la primera prueba (solo una vez)
        if (!session('sergio_intro_visto')) {
            session(['sergio_intro_visto' => true]);
            return view('sergio_intro');
        }

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();
        $this->logAccion($partida, config('fases.fases.prueba1.db'), 'Entra a la prueba');
        $yaUsada = in_array(1, $this->obtenerPistasUsadas($partida));
        $pistaConfig = config('escape.pistas.1');
        return view('pruebas.prueba1', compact('tiempo_transcurrido', 'yaUsada', 'pistaConfig'));
    }

    public function validarPrueba1(ValidarPrueba1Request $request)
    {
        if (session('prueba1mario_superada')) {
            return response()->json(['success' => true]);
        }

        $partida = $this->timerService->obtenerPartidaActiva();

        if (!$request->completed) {
            $this->logAccion($partida, config('fases.fases.prueba1.db'), 'Intento fallido', ['motivo' => 'Faltan acertijos por resolver']);
            return response()->json(['success' => false, 'message' => 'Validación fallida: Faltan acertijos por resolver'], 400);
        }

        // Respuestas correctas normalizadas esperadas (ordenadas por acertijo)
        $respuestas_correctas = config('escape.respuestas.prueba1');

        foreach ($request->answers as $index => $answer) {
            if (!isset($respuestas_correctas[$index])) {
                $this->logAccion($partida, config('fases.fases.prueba1.db'), 'Intento fallido', ['motivo' => 'Sobran respuestas']);
                return response()->json(['success' => false, 'message' => 'Validación fallida: Sobran respuestas.'], 400);
            }
            if (!in_array(strtolower(trim($answer)), $respuestas_correctas[$index])) {
                $this->logAccion($partida, config('fases.fases.prueba1.db'), 'Intento fallido', ['motivo' => 'Respuesta '.($index + 1).' incorrecta: '.$answer]);
                return response()->json(['success' => false, 'message' => 'Validación fallida: Respuesta '.($index + 1).' incorrecta.'], 400);
            }
        }

        // Esta función recibe el AJAX (fetch) de la validación
        session(['prueba1mario_superada' => true]);

        // SOLO actualizamos la fase_actual, NO paramos el reloj maestro de BBDD.
        if ($partida) {
            $this->logAccion($partida, config('fases.fases.prueba1.db'), 'Prueba superada');

            // LOGRO 1: Speedrunner Ninja (Prueba 1 superada en menos de 2 minutos sin penalizaciones)
            $tiempo = $this->timerService->obtenerTiempoTranscurrido();
            if ($tiempo < 120) {
                $this->desbloquearLogro($partida, 'speedrunner_ninja');
            }

            $this->timerService->avanzarFase($partida, config('fases.fases.prueba2.db'), 'Prueba 1 Mario Superada');
            $this->broadcastDashboard();
        }

        // Devolvemos un OK en formato JSON
        return response()->json(['success' => true]);
    }

    // =====================================================
    // PRUEBA 2: EL CLÚSTER FRAGMENTADO (Expendedoras)
    // =====================================================
    public function mostrarPrueba2()
    {

        if ($this->timerService->verificarTimeout()) return redirect()->route('final')->with('timeout', true);

        $partida = $this->timerService->obtenerPartidaActiva();
        if ($redir = $this->redirigirSiYaSuperada($partida, 2)) return $redir;

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();
        $this->logAccion($partida, config('fases.fases.prueba2.db'), 'Entra a la prueba');
        $yaUsada = in_array(2, $this->obtenerPistasUsadas($partida));
        $pistaConfig = config('escape.pistas.2');
        return view('pruebas.prueba2', compact('tiempo_transcurrido', 'yaUsada', 'pistaConfig'));
    }

    public function validarPrueba2(Request $request)
    {
        if (session('prueba2yoseth_superada')) {
            return response()->json(['success' => true]);
        }

        $respuestaCorrecta = config('escape.respuestas.prueba2');
        $input = strtolower(trim($request->input('answer', '')));
        // Normalizar: quitar acentos
        $input = preg_replace('/\pM/u', '', \Normalizer::normalize($input, \Normalizer::FORM_D));

        if ($input !== $respuestaCorrecta) {
            return response()->json(['success' => false, 'message' => 'Respuesta incorrecta.']);
        }

        session(['prueba2yoseth_superada' => true]);

        // AHORA SÍ: NO VAMOS AL FINAL AÚN. VAMOS A LA PRUEBA 3.
        $partida = $this->timerService->obtenerPartidaActiva();

        if ($partida && $partida->inicio_juego) {
            $this->logAccion($partida, config('fases.fases.prueba2.db'), 'Prueba superada');
            $this->timerService->avanzarFase($partida, config('fases.fases.prueba3.db'), 'Prueba 2 Yoseth Superada');
            $this->broadcastDashboard();
        }

        return response()->json(['success' => true]);
    }

    // =====================================================
    // PRUEBA 3: OPERACIÓN CAFEÍNA
    // =====================================================
    public function mostrarPrueba3()
    {

        if ($this->timerService->verificarTimeout()) return redirect()->route('final')->with('timeout', true);

        $partida = $this->timerService->obtenerPartidaActiva();
        if ($redir = $this->redirigirSiYaSuperada($partida, 3)) return $redir;

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();
        $this->logAccion($partida, config('fases.fases.prueba3.db'), 'Entra a la prueba');
        $yaUsada = in_array(3, $this->obtenerPistasUsadas($partida));
        $pistaConfig = config('escape.pistas.3');
        return view('pruebas.prueba3', compact('tiempo_transcurrido', 'yaUsada', 'pistaConfig'));
    }

    public function validarPrueba3(ValidarPrueba3Request $request)
    {
        if (session('prueba3angel_superada')) {
            return response()->json(['success' => true]);
        }

        $CHECKSUM_CORRECTO = config('escape.respuestas.prueba3'); // 18 botones + 80 cents + 5 azucar
        $partida = $this->timerService->obtenerPartidaActiva();

        // Normalizar entrada: trim espacios y convertir a integer
        $respuesta_usuario = (int)trim($request->checksum);

        if ($respuesta_usuario !== $CHECKSUM_CORRECTO) {
            $this->logAccion($partida, config('fases.fases.prueba3.db'), 'Intento fallido', ['input' => $request->checksum]);
            return response()->json(['success' => false, 'message' => 'Checksum erróneo']);
        }

        session(['prueba3angel_superada' => true]);

        // Ya no es la última, pasamos a Prueba 4

        if ($partida && $partida->inicio_juego) {
            $this->logAccion($partida, config('fases.fases.prueba3.db'), 'Prueba superada');
            $this->timerService->avanzarFase($partida, config('fases.fases.prueba4.db'), 'Prueba 3 Angel Superada');
            $this->broadcastDashboard();
        }

        // Mensaje final desde Back
        $mensajeFinal = "Has purgado la red de cafeína. El sistema vuelve a fluir, pero el rastro digital\nno termina aquí. La siguiente pieza del puzzle te espera donde los snacks\nson liberados de su encierro.";

        return response()->json([
            'success' => true,
            'mensaje' => $mensajeFinal
        ]);
    }

    // =====================================================
    // PRUEBA 4: OPERACIÓN REPUESTOS (Oasis)
    // =====================================================
    public function mostrarPrueba4()
    {

        if ($this->timerService->verificarTimeout()) return redirect()->route('final')->with('timeout', true);

        $partida = $this->timerService->obtenerPartidaActiva();
        if ($redir = $this->redirigirSiYaSuperada($partida, 4)) return $redir;

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();
        $this->logAccion($partida, config('fases.fases.prueba4.db'), 'Entra a la prueba');
        $yaUsada = in_array(4, $this->obtenerPistasUsadas($partida));
        $pistaConfig = config('escape.pistas.4');
        return view('pruebas.prueba4', compact('tiempo_transcurrido', 'yaUsada', 'pistaConfig'));
    }

    public function validarPrueba4(ValidarPrueba4Request $request)
    {
        if (session('prueba4arturo_superada')) {
            return response()->json(['success' => true]);
        }

        $CHECKSUM_OASIS = config('escape.respuestas.prueba4'); // Ej: 6 filas + cod 128 cafe + 7 items A1 -> Total manual: 141
        $partida = $this->timerService->obtenerPartidaActiva();

        // Normalizar entrada: trim espacios y convertir a integer
        $respuesta_usuario = (int)trim($request->checksum);

        if ($respuesta_usuario !== $CHECKSUM_OASIS) {
            $this->logAccion($partida, config('fases.fases.prueba4.db'), 'Intento fallido', ['input' => $request->checksum]);
            return response()->json(['success' => false, 'message' => 'Checksum erróneo']);
        }

        session(['prueba4arturo_superada' => true]);

        // Ya no es la última, pasamos a Prueba 5

        if ($partida && $partida->inicio_juego) {
            $this->logAccion($partida, config('fases.fases.prueba4.db'), 'Prueba superada');
            $this->timerService->avanzarFase($partida, config('fases.fases.prueba5.db'), 'Prueba 4 Arturo Superada');
            $this->broadcastDashboard();
        }

        // Mensaje final desde Back
        $mensajeFinal = "El orden natural ha desaparecido. Lo que empieza por A ahora termina en Z.\nEl archivo maestro ha sido fragmentado y ocultado en 64 nodos metálicos.";

        return response()->json([
            'success' => true,
            'mensaje' => $mensajeFinal
        ]);
    }

    // =====================================================
    // PRUEBA 5: PROTOCOLO ESPEJO (Taquillas)
    // =====================================================
    public function mostrarPrueba5()
    {

        if ($this->timerService->verificarTimeout()) return redirect()->route('final')->with('timeout', true);

        $partida = $this->timerService->obtenerPartidaActiva();
        if ($redir = $this->redirigirSiYaSuperada($partida, 5)) return $redir;

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();
        $this->logAccion($partida, config('fases.fases.prueba5.db'), 'Entra a la prueba');
        $yaUsada = in_array(5, $this->obtenerPistasUsadas($partida));
        $pistaConfig = config('escape.pistas.5');
        return view('pruebas.prueba5', compact('tiempo_transcurrido', 'yaUsada', 'pistaConfig'));
    }

    public function validarPrueba5(ValidarPrueba5Request $request)
    {
        if (session('prueba5eloy_superada')) {
            return response()->json(['success' => true]);
        }

        $PALABRA_ESPEJO = config('escape.respuestas.prueba5');
        $input_normalizado = strtolower(trim($request->checksum));
        $partida = $this->timerService->obtenerPartidaActiva();

        // LOGRO 3: Hackerman Easter Egg
        if ($input_normalizado === 'sudo rm -rf') {
            $this->desbloquearLogro($partida, 'hackerman');
            $this->logAccion($partida, config('fases.fases.prueba5.db'), 'Easter Egg Ejecutado', ['input' => $input_normalizado]);
            return response()->json(['success' => false, 'message' => 'Comando peligroso detectado. Permiso denegado. Logro [HACKERMAN] desbloqueado.']);
        }

        if ($input_normalizado !== $PALABRA_ESPEJO) {
            $this->logAccion($partida, config('fases.fases.prueba5.db'), 'Intento fallido', ['input' => $input_normalizado]);
            return response()->json(['success' => false, 'message' => 'Palabra clave errónea']);
        }

        session(['prueba5eloy_superada' => true]);

        // AHORA SÍ, PASAMOS A LA PRUEBA 6 (MÍGUE)

        if ($partida && $partida->inicio_juego) {
            $this->logAccion($partida, config('fases.fases.prueba5.db'), 'Prueba superada');
            $this->timerService->avanzarFase($partida, config('fases.fases.prueba6.db'), 'Prueba 5 Eloy Superada');
            $this->broadcastDashboard();
        }

        // Mensaje final desde Back
        $mensajeFinal = "El orden ha sido restaurado. El sistema vuelve a reconocer el abecedario\noriginal. Pero el núcleo del DAWSCAPE está inestable...\nLa siguiente anomalía te espera en la terminal principal, donde\nse debe calibrar manualmente la sobrecarga.";

        return response()->json([
            'success' => true,
            'mensaje' => $mensajeFinal
        ]);
    }

    // =====================================================
    // PRUEBA 6: SOBRECARGA DEL SISTEMA (Mígue - Simon Dice Cooperativo)
    // =====================================================
    public function mostrarPrueba6()
    {

        if ($this->timerService->verificarTimeout()) return redirect()->route('final')->with('timeout', true);

        $partida = $this->timerService->obtenerPartidaActiva();
        if ($redir = $this->redirigirSiYaSuperada($partida, 6)) return $redir;

        // Generar secuencia en el backend (ej: 15 pasos)
        $maxSteps = 15;
        $sequence = [];
        for ($i = 0; $i < $maxSteps; $i++) {
            $sequence[] = rand(0, 3);
        }

        // Guardar secuencia en sesión
        session(['prueba6_sequence' => $sequence]);

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();
        $this->logAccion($partida, config('fases.fases.prueba6.db'), 'Entra a la prueba');
        $yaUsada = in_array(6, $this->obtenerPistasUsadas($partida));
        $pistaConfig = config('escape.pistas.6');
        return view('pruebas.prueba6', compact('tiempo_transcurrido', 'yaUsada', 'pistaConfig'));
    }

    public function validarPrueba6(ValidarPrueba6Request $request)
    {
        if (session('prueba6migue_superada')) {
            return response()->json(['success' => true]);
        }

        $partida = $this->timerService->obtenerPartidaActiva();

        if (!$request->completed || !is_array($request->expected_sequence)) {
            $this->logAccion($partida, config('fases.fases.prueba6.db'), 'Intento fallido', ['motivo' => 'Datos incompletos']);
            return response()->json(['success' => false, 'message' => 'Validación fallida: Datos incompletos.'], 400);
        }

        $sessionSequence = session('prueba6_sequence', []);

        if (empty($sessionSequence) || $request->expected_sequence !== $sessionSequence) {
            $this->logAccion($partida, config('fases.fases.prueba6.db'), 'Intento fallido', ['motivo' => 'Secuencia incorrecta']);
            return response()->json(['success' => false, 'message' => 'Validación fallida: Secuencia incorrecta o manipulada.'], 400);
        }

        session(['prueba6migue_superada' => true]);

        // FINALIZA LA PRUEBA 6 -> AVANZAMOS A PRUEBA 7
        if ($partida && $partida->inicio_juego) {
            $this->logAccion($partida, config('fases.fases.prueba6.db'), 'Prueba superada');

            // LOGRO 2: Cerebro Intacto (No usar pistas)
            if (($partida->penalizacion_segundos ?? 0) == 0) {
                $this->desbloquearLogro($partida, 'cerebro_intacto');
            }

            $this->timerService->avanzarFase($partida, config('fases.fases.prueba7.db'), 'Prueba 6 Migue Superada');
            $this->broadcastDashboard();
        }

        $mensajeFinal = "Núcleo temporal estabilizado exitosamente. Pero el sistema central ha desplegado un último Cortafuegos de Conocimiento antes de liberar las cierres magnéticos.";

        return response()->json([
            'success' => true,
            'mensaje' => $mensajeFinal
        ]);
    }

    // =====================================================
    // PENALIZACIÓN POR FALLO EN SIMÓN DICE
    // =====================================================
    public function penalizarPrueba6(Request $request)
    {
        $partida = $this->timerService->obtenerPartidaActiva();

        if (!$partida) {
            return response()->json(['success' => false, 'message' => 'No hay partida activa.'], 403);
        }

        // Penalizar con 30 segundos
        $penalizacion = 30;
        $partida->increment('penalizacion_segundos', $penalizacion);

        $this->logAccion($partida, config('fases.fases.prueba6.db'), 'Penalización por fallo en Simón Dice', [
            'penalizacion' => $penalizacion,
            'mensaje' => '[PENALIZACIÓN] Secuencia incorrecta. +30 segundos añadidos al tiempo.'
        ]);

        return response()->json([
            'success' => true,
            'penalizacion' => $penalizacion
        ]);
    }

    // =====================================================
    // PRUEBA 7: OPERACIÓN EL CÓDICE OCULTO — Cerradura Digital + QRs físicos
    // =====================================================

    /**
     * Base de datos de dígitos: cada dígito tiene una ubicación física y un acertijo.
     * Modifica aquí ubicaciones y acertijos sin tocar la vista.
     */
    private const DIGITOS_CODICE = [
        0 => [
            'ubicacion' => 'Mostrador de secretaría',
            'acertijo'  => 'No valgo absolutamente nada si me pones a la izquierda, pero multiplico tu riqueza si me pones a la derecha. Fui el último dígito en inventarse, nacido en la India para representar la nada y el vacío absoluto.'
        ],
        1 => [
            'ubicacion' => 'Mesa de Nescafé cafetería',
            'acertijo'  => 'Soy el único número cuyo cuadrado, cubo y raíz son exactamente idénticos a mí mismo. En la tabla periódica, corono al elemento más ligero y abundante de todo el universo.'
        ],
        2 => [
            'ubicacion' => 'Aseos de cafetería',
            'acertijo'  => 'En informática, soy la base fundamental de todo el lenguaje que entienden las máquinas. Soy la excepción a la regla: el único número par que tiene el honor de ser primo.'
        ],
        3 => [
            'ubicacion' => 'Extintor final del pasillo junto a la cristalera del LAB 103',
            'acertijo'  => 'Newton necesitó exactamente este número de leyes para explicar el movimiento del universo. Si quieres definir un plano geométrico perfecto en el espacio sin que cojee, me necesitas estrictamente.'
        ],
        4 => [
            'ubicacion' => 'Clase vacía esquina donde las escaleras interiores que no funcionan',
            'acertijo'  => 'Lato sin descanso en tu pecho gracias a mis cavidades anatómicas. En la genética, soy el total de letras químicas (A, C, G, T) que escriben el libro de la vida.'
        ],
        5 => [
            'ubicacion' => 'Máquinas de vending planta 1 junto a los aseos de emergencias en el Oasis',
            'acertijo'  => 'En la geometría clásica, represento el total de sólidos platónicos perfectos que existen. En el emblema de los Juegos Olímpicos, doy forma a los anillos que representan las partes habitadas del mundo.'
        ],
        6 => [
            'ubicacion' => 'Puertas X EMERGENCIAS+PROTECCION CIVIL junto al cuadro de luces',
            'acertijo'  => 'Soy el primer número perfecto, la suma de mis divisores exactos (1, 2 y 3) te dará mi valor. En química, soy el cimiento de la vida, el número atómico del carbono.'
        ],
        7 => [
            'ubicacion' => 'Taquillas Oasis planta 2',
            'acertijo'  => 'Químicamente hablando, soy la neutralidad perfecta, el pH del agua pura. Anatómicamente, soy el número de vértebras cervicales que comparte un humano con una jirafa.'
        ],
        8 => [
            'ubicacion' => 'Aseos cerrados donde el cartel sin salida y la plancha amarilla que cierra el paso',
            'acertijo'  => 'En el mundo digital, formo la unidad básica de memoria (el byte) cuando agrupo a mis pequeños soldados (los bits). Soy el número de planetas reconocidos oficialmente en nuestro sistema solar.'
        ],
        9 => [
            'ubicacion' => 'Plantas de al lado de las escaleras en la planta 0',
            'acertijo'  => 'Tengo una maldición: multiplícame por el entero que quieras; si sumas las cifras del resultado, siempre volverás a mí. Soy el total de círculos del Infierno de Dante.'
        ],
    ];

    public function mostrarPrueba7()
    {
        if ($this->timerService->verificarTimeout()) return redirect()->route('final')->with('timeout', true);

        $partida = $this->timerService->obtenerPartidaActiva();
        if ($redir = $this->redirigirSiYaSuperada($partida, 7)) return $redir;

        // Generar y persistir combinación única por equipo si aún no existe
        if ($partida && empty($partida->combinacion_codice)) {
            $digitos = range(0, 9);
            shuffle($digitos);
            $combinacion = array_slice($digitos, 0, 4); // 4 dígitos únicos sin repetir
            $partida->update(['combinacion_codice' => $combinacion, 'intentos_codice' => 0]);
        }

        $combinacion = $partida ? ($partida->combinacion_codice ?? [0,1,2,3]) : [0,1,2,3];

        // Construir array de pistas (ubicaciones) para los 4 dígitos de esta partida
        $pistas = array_map(function($digito) {
            return self::DIGITOS_CODICE[$digito]['ubicacion'];
        }, $combinacion);

        $intentos_fallidos = $partida ? ($partida->intentos_codice ?? 0) : 0;

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();
        $this->logAccion($partida, config('fases.fases.prueba7.db'), 'Entra a la prueba (Códice Oculto)');
        $yaUsada = in_array(7, $this->obtenerPistasUsadas($partida));
        $pistaConfig = config('escape.pistas.7');

        return view('pruebas.prueba7', compact('tiempo_transcurrido', 'pistas', 'intentos_fallidos', 'yaUsada', 'pistaConfig'));
    }

    public function validarPrueba7(Request $request)
    {
        if (session('prueba7_superada')) {
            return response()->json(['success' => true]);
        }

        $partida = $this->timerService->obtenerPartidaActiva();

        if (!$partida) {
            return response()->json(['success' => false, 'message' => 'Sesión no encontrada.'], 401);
        }

        $combinacionCorrecta = $partida->combinacion_codice;

        if (!$combinacionCorrecta || count($combinacionCorrecta) !== 4) {
            $this->logAccion($partida, config('fases.fases.prueba7.db'), 'Error: combinación no inicializada');
            return response()->json(['success' => false, 'message' => 'Error interno: combinación no inicializada.'], 500);
        }

        $digitsInput = $request->input('digits', []);

        // Normalizar: convertir a enteros
        $digitsInput = array_map('intval', array_values((array) $digitsInput));

        if (count($digitsInput) !== 4) {
            return response()->json(['success' => false, 'message' => 'Debes introducir exactamente 4 dígitos.']);
        }

        if ($digitsInput === array_map('intval', $combinacionCorrecta)) {
            // ✅ COMBINACIÓN CORRECTA
            session(['prueba7_superada' => true]);

            if ($partida->inicio_juego) {
                $this->logAccion($partida, config('fases.fases.prueba7.db'), 'Códice Oculto — COMBINACIÓN CORRECTA');
                // Avanzamos a Prueba 8 (Cultura General)
                $this->timerService->avanzarFase($partida, config('fases.fases.prueba8.db'), 'Prueba 7 Dinamica Superada');
                $this->broadcastDashboard();
            }

            return response()->json(['success' => true]);
        }

        // ❌ COMBINACIÓN INCORRECTA — incrementar contador
        $nuevosIntentos = ($partida->intentos_codice ?? 0) + 1;
        $partida->update(['intentos_codice' => $nuevosIntentos]);

        $this->logAccion($partida, config('fases.fases.prueba7.db'), 'Intento fallido', [
            'intento'  => $nuevosIntentos,
            'enviado'  => $digitsInput,
            'correcto' => $combinacionCorrecta
        ]);

        return response()->json([
            'success'  => false,
            'intentos' => $nuevosIntentos
        ]);
    }

    // =====================================================
    // PRUEBA 8: CULTURA GENERAL (CORTAFUEGOS)
    // =====================================================
    public function mostrarPrueba8()
    {
        if ($this->timerService->verificarTimeout()) return redirect()->route('final')->with('timeout', true);

        $partida = $this->timerService->obtenerPartidaActiva();
        if ($redir = $this->redirigirSiYaSuperada($partida, 8)) return $redir;

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();
        $this->logAccion($partida, config('fases.fases.prueba8.db'), 'Entra a la prueba (Cortafuegos)');

        $idsGuardados = session('prueba8_preguntas');

        if (empty($idsGuardados)) {
            $preguntasAsignadas = PreguntaDinamica::where('activa', true)
                ->inRandomOrder()
                ->limit(5)
                ->get();
            session(['prueba8_preguntas' => $preguntasAsignadas->pluck('id')->toArray()]);
        } else {
            $preguntasAsignadas = PreguntaDinamica::whereIn('id', $idsGuardados)
                ->get()
                ->sortBy(function($model) use ($idsGuardados) {
                    return array_search($model->id, $idsGuardados);
                })->values();
        }

        return view('pruebas.prueba8', compact('tiempo_transcurrido', 'preguntasAsignadas'));
    }

    public function validarPrueba8(Request $request)
    {
        if (session('prueba8_superada')) {
            session()->forget('prueba8_preguntas');
            return response()->json(['success' => true]);
        }

        $partida = $this->timerService->obtenerPartidaActiva();
        if (!$partida) {
            return response()->json(['success' => false, 'message' => 'Sesión no encontrada.'], 401);
        }

        // Protección idempotente: si la BBDD ya refleja que esta prueba fue superada
        // (ej: un POST anterior tuvo éxito pero la respuesta HTTP se perdió por red),
        // marcamos la sesión y devolvemos éxito sin re-validar.
        $faseConfig = collect(config('fases.fases'))->firstWhere('db', $partida->fase_actual);
        $pesoActualDB = $faseConfig['peso'] ?? 0;
        if ($pesoActualDB > 8) {
            session(['prueba8_superada' => true]);
            session()->forget('prueba8_preguntas');
            return response()->json(['success' => true]);
        }

        if (!$request->boolean('completed')) {
            return response()->json(['success' => false, 'message' => 'Validación fallida.'], 400);
        }

        $ids = session('prueba8_preguntas', []);
        $respuestasUsuario = $request->input('respuestas_usuario', []);

        if (count($ids) !== 5 || count($respuestasUsuario) !== 5) {
            \Log::error('Validacion prueba 8 fallida - incompletos', ['ids' => $ids, 'resp' => $respuestasUsuario]);
            return response()->json(['success' => false, 'message' => 'Datos de validación incompletos o corruptos.'], 400);
        }

        $preguntas = PreguntaDinamica::whereIn('id', $ids)->get()->keyBy('id');

        foreach ($ids as $index => $id) {
            $pregunta = $preguntas[$id] ?? null;
            if (!$pregunta) {
                 return response()->json(['success' => false, 'message' => 'Pregunta no encontrada en la base de datos.'], 400);
            }

            $respuestaUsuario = strtolower(trim($respuestasUsuario[$index] ?? ''));
            $respuestaCorrecta = strtolower(trim($pregunta->respuesta_correcta));
            $alternativas = array_map('strtolower', $pregunta->respuestas_alternativas ?? []);

            // Como algunas migraciones antiguas guardan las alternativas separadas por comas en la respuesta correcta
            // emulamos la lógica del frontend que divide por comas si existen
            if (str_contains($respuestaCorrecta, ',')) {
                $extras = array_map('trim', explode(',', $respuestaCorrecta));
                $alternativas = array_merge($alternativas, $extras);
            }

            $normalizer = function($str) {
                if (class_exists(\Normalizer::class)) {
                    $str = \Normalizer::normalize($str, \Normalizer::FORM_D);
                    $str = preg_replace('/\pM/u', '', $str);
                }
                return $str;
            };

            $respuestaUsuario = $normalizer($respuestaUsuario);
            $respuestaCorrecta = $normalizer($respuestaCorrecta);
            $alternativas = array_map($normalizer, $alternativas);

            $esCorrecta = $respuestaUsuario === $respuestaCorrecta || in_array($respuestaUsuario, $alternativas);

            if (!$esCorrecta) {
                \Log::error('Validacion prueba 8 fallida - incorrectas', [
                    'id' => $id, 
                    'user' => $respuestaUsuario,
                    'correcta' => $respuestaCorrecta,
                    'alternativas' => $alternativas
                ]);
                return response()->json(['success' => false, 'message' => 'Validación fallida en el servidor. Respuestas incorrectas.'], 400);
            }
        }

        session(['prueba8_superada' => true]);
        
        // Limpiamos la sesión de preguntas una vez superada la prueba exitosamente
        session()->forget('prueba8_preguntas');

        $this->logAccion($partida, config('fases.fases.prueba8.db'), 'Prueba superada');

        // Avanzamos a Prueba 9 (Juego de Pistas)
        $this->timerService->avanzarFase($partida, config('fases.fases.prueba9.db'), 'Prueba 8 Cultura General Superada');
        $this->broadcastDashboard();

        return response()->json(['success' => true]);
    }

    // =====================================================
    // PRUEBA 9: JUEGO DE PISTAS
    // =====================================================
    public function mostrarPrueba9()
    {
        if ($this->timerService->verificarTimeout()) return redirect()->route('final')->with('timeout', true);

        $partida = $this->timerService->obtenerPartidaActiva();
        if ($redir = $this->redirigirSiYaSuperada($partida, 9)) return $redir;

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();
        $this->logAccion($partida, config('fases.fases.prueba9.db'), 'Entra a la prueba');

        $index = (int) session('prueba9_index', 0);
        $letters = (array) session('prueba9_letters', []);
        $fails = (int) session('prueba9_intentos_fallidos', 0);

        return view('pruebas.prueba9', [
            'tiempo_transcurrido' => $tiempo_transcurrido,
            'progress' => [
                'index' => $index,
                'letters' => array_values($letters),
                'fails' => $fails,
            ],
        ]);
    }

    private function normalizarRespuesta(string $str): string
    {
        $s = trim(mb_strtolower($str));
        if (class_exists(\Normalizer::class)) {
            $s = \Normalizer::normalize($s, \Normalizer::FORM_D);
            $s = preg_replace('/\pM/u', '', $s);
        }
        $s = preg_replace('/\s+/', ' ', $s);
        return $s;
    }

    public function validarPrueba9(Request $request)
    {
        if (session('prueba9_superada')) {
            return response()->json(['success' => true, 'state' => 'done']);
        }

        $partida = $this->timerService->obtenerPartidaActiva();
        if (!$partida) {
            return response()->json(['success' => false, 'message' => 'Sesión no encontrada.'], 401);
        }

        $questions = [
            ['q' => 'El director del centro se llama E_____o. Su nombre tiene 7 letras y es muy común en España. ¿Cómo se llama?', 'a' => 'eduardo', 'l' => 'N'],
            ['q' => 'Contad las ventanas de la clase de DAW. El número está entre 4 y 6.', 'a' => '5', 'l' => 'A'],
            ['q' => 'Vuestro centro tiene planta baja, primera y segunda planta. ¿Cuántas plantas tiene en total?', 'a' => '3', 'l' => 'C'],
            ['q' => 'En todo el centro, ¿cuántas clases están dedicadas exclusivamente a DAW (Desarrollo de Aplicaciones Web)?', 'a' => '1', 'l' => 'H'],
            ['q' => '¿Qué marca coreana famosa por los móviles Galaxy y las TV QLED invierte en tu FP a través del programa XTART?', 'a' => 'samsung', 'l' => 'O'],
        ];
        $finalPassword = 'nacho';

        $action = (string) $request->input('action', 'answer');
        $index = (int) session('prueba9_index', 0);
        $letters = (array) session('prueba9_letters', []);
        $fails = (int) session('prueba9_intentos_fallidos', 0);

        if ($action === 'answer') {
            if ($index >= count($questions)) {
                return response()->json(['success' => true, 'state' => 'password', 'letters' => array_values($letters), 'total' => count($questions)]);
            }

            $user = $this->normalizarRespuesta((string) $request->input('answer', ''));
            $expected = $this->normalizarRespuesta($questions[$index]['a']);

            if ($user === '' || $user !== $expected) {
                $fails++;
                session(['prueba9_intentos_fallidos' => $fails]);

                // Persistir métricas en BBDD
                $partida->increment('intentos_prueba9', 1);
                if ($fails >= 5 && !$partida->hints_used) {
                    $partida->update(['hints_used' => true]);
                }

                return response()->json([
                    'success' => false,
                    'message' => '[ERROR] Respuesta incorrecta. Inténtalo de nuevo.',
                    'fails' => $fails,
                    'hint' => $fails >= 5 ? '[PISTA DEL SISTEMA] — Piensa en lo que ves cada día en el instituto.' : null,
                ]);
            }

            // Correcta
            $letters[] = $questions[$index]['l'];
            $index++;
            session([
                'prueba9_letters' => array_values($letters),
                'prueba9_index' => $index,
            ]);

            // No registramos hitos por cada acierto (solo al completar la prueba entera)

            if ($index >= count($questions)) {
                return response()->json([
                    'success' => true,
                    'state' => 'password',
                    'letter' => $questions[$index - 1]['l'],
                    'letters' => array_values($letters),
                ]);
            }

            return response()->json([
                'success' => true,
                'state' => 'question',
                'letter' => $questions[$index - 1]['l'],
                'nextIndex' => $index,
                'total' => count($questions),
            ]);
        }

        if ($action === 'password') {
            if ($index < count($questions) || count($letters) < count($questions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes completar todas las preguntas antes de introducir la contraseña.',
                ], 400);
            }

            $pass = $this->normalizarRespuesta((string) $request->input('password', ''));
            if ($pass !== $finalPassword) {
                $fails++;
                session(['prueba9_intentos_fallidos' => $fails]);

                // Persistir métricas en BBDD
                $partida->increment('intentos_prueba9', 1);
                if ($fails >= 5 && !$partida->hints_used) {
                    $partida->update(['hints_used' => true]);
                }

                return response()->json([
                    'success' => false,
                    'message' => '[ERROR] Contraseña incorrecta. Inténtalo de nuevo.',
                    'fails' => $fails,
                    'hint' => $fails >= 5 ? '[PISTA DEL SISTEMA] — Piensa en lo que ves cada día en el instituto.' : null,
                ]);
            }

            session(['prueba9_superada' => true]);
            $this->logAccion($partida, config('fases.fases.prueba9.db'), 'Prueba completada');

            $segundos_totales = $this->timerService->obtenerTiempoTranscurrido();

            // Registrar hito de superación de la Prueba 9 antes de finalizar
            \App\Models\HitoFase::create([
                'partida_id'            => $partida->id,
                'fase'                  => 'Prueba 9 Juego de Pistas Superada',
                'segundos_transcurridos' => $segundos_totales,
                'alcanzado_at'          => Carbon::now()
            ]);

            // Limpiar estado de sesión de la prueba 9 para evitar arrastre al recargar/rejugar
            session()->forget(['prueba9_index', 'prueba9_letters', 'prueba9_intentos_fallidos']);

            $partida->update([
                'tiempo_segundos' => $segundos_totales,
                'fase_actual'     => config('fases.fases.finalizado.db'),
            ]);

            $this->broadcastDashboard();

            return response()->json(['success' => true, 'state' => 'done']);
        }

        return response()->json(['success' => false, 'message' => 'Acción no válida.'], 400);
    }

    // --- PANTALLA FINAL ---
    public function mostrarFinal()
    {
        // Traemos los datos REALES de la BBDD
        $partida = Partida::where('email', session('email'))->first();

        // Top 3 equipos finalizados, ordenados por tiempo
        $top3 = Partida::where('fase_actual', config('fases.fases.finalizado.db'))
            ->orderBy('tiempo_segundos', 'asc')
            ->limit(3)
            ->get();

        // Calcular los deltas de tiempo por cada prueba (Performance breakdown)
        $hitos = [];
        if ($partida) {
            $hitos_db = \App\Models\HitoFase::where('partida_id', $partida->id)
                ->where('fase', '!=', 'JUEGO FINALIZADO') // legacy: ya no se crea, pero filtramos por compatibilidad
                ->orderBy('segundos_transcurridos', 'asc')
                ->orderBy('id', 'asc')
                ->get()
                ->unique('fase') // legacy: si una partida antigua duplicó una prueba, nos quedamos con el primero
                ->values();

            $last_seconds = 0;
            foreach ($hitos_db as $hito) {
                // El hito representa cuándo se *completó* o se *llegó* a una fase.
                $delta = $hito->segundos_transcurridos - $last_seconds;
                $hitos[] = [
                    'fase' => $hito->fase,
                    'segundos_invertidos' => $delta,
                    'total_acumulado' => $hito->segundos_transcurridos
                ];
                $last_seconds = $hito->segundos_transcurridos;
            }
        }

        return view('final', compact('partida', 'top3', 'hitos'));
    }

    // --- VALIDACIONES ---
    // (Pruedas 1 y 2 fueron eliminadas a petición del cliente)

    // --- PRUEBA: AULA 115 (Ordenadores y número de serie) ---
    public function mostrarAula115()
    {
        // Accesible sin login: si hay sesión usa el timer de BBDD, si no empieza en 0
        $tiempo_transcurrido = session()->has('email')
            ? $this->timerService->obtenerTiempoTranscurrido()
            : 0;

        return view('pruebas.aula-115', compact('tiempo_transcurrido'));
    }

    public function completarAula115(Request $request)
    {
        // Si hay sesión activa, registrar en BBDD
        if (session()->has('email')) {
            $partida = Partida::where('email', session('email'))->first();

            if ($partida) {
                $inicio = Carbon::parse($partida->inicio_juego);
                $segundos_totales = abs(Carbon::now()->diffInSeconds($inicio));

                $partida->update([
                    'tiempo_segundos' => $segundos_totales,
                    'fase_actual' => config('fases.fases.finalizado.db')
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Escape room completado']);
    }

    // MÉTODOS DE QRs EXTRAÍDOS A QrController

    // MÉTODOS DE ADMIN EXTRAÍDOS A AdminController
}
