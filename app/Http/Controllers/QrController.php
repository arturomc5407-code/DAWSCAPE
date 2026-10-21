<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use Carbon\Carbon;
use App\Services\GameTimerService;
use App\Http\Requests\GuardarQrRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class QrController extends Controller
{
    protected $timerService;

    public function __construct(GameTimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    // =====================================================
    // VALIDACIÓN DE CÓDIGOS QR (texto plano) Pruebas 3-5
    // Endpoint: POST /validar-codigo-qr (auth + throttle)
    // =====================================================
    public function validar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'codigo' => ['required', 'string'],
            'prueba' => ['required', 'integer', 'between:3,5'],
        ]);

        // Normalizar texto QR: quitar invisibles/espacios y forzar mayúsculas
        $codigoRaw = (string) $data['codigo'];
        $codigoSanitizado = preg_replace('/[[:^print:]]/u', '', $codigoRaw) ?? $codigoRaw; // quita \r \n etc.
        $codigoSanitizado = trim($codigoSanitizado);
        $codigo = strtoupper($codigoSanitizado);
        $prueba = (int) $data['prueba'];

        // Anti-salto: alinear con las claves reales de progreso del JuegoController
        // (prueba2yoseth_superada, prueba3angel_superada, etc.)
        $keysSuperada = [
            1 => 'prueba1mario_superada',
            2 => 'prueba2yoseth_superada',
            3 => 'prueba3angel_superada',
            4 => 'prueba4arturo_superada',
            5 => 'prueba5eloy_superada',
        ];

        $keyAnteriorSuperada = $keysSuperada[$prueba - 1] ?? null;
        if ($keyAnteriorSuperada && !session()->get($keyAnteriorSuperada, false)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta prueba todavía. Completa la prueba anterior.',
            ], 403);
        }

        // Si ya se validó este QR antes, devolvemos éxito idempotente
        $keyActualCompletada = 'prueba_' . $prueba . '_completada';
        if (session()->get($keyActualCompletada, false)) {
            return response()->json([
                'success' => true,
                'redirect_url' => $this->redirectUrlParaPrueba($prueba),
            ]);
        }

        // Códigos secretos válidos.
        // Comprobamos si el código mandado (que puede ser la URL completa o el código plano) coincide
        $codigosValidos = [
            3 => 'ACCESO_FASE_3',
            4 => 'ACCESO_FASE_4',
            5 => 'ACCESO_FASE_5',
        ];

        $urlsAdmitidas = [
            3 => 'PRUEBA3ANGEL',
            4 => 'PRUEBA4ARTURO',
            5 => 'PRUEBA5ELOY',
        ];

        if (!array_key_exists($prueba, $codigosValidos)) {
            return response()->json([
                'success' => false,
                'message' => 'Prueba no válida.',
            ], 422);
        }

        $claveEsperada = $codigosValidos[$prueba];
        $urlAdmitida = $urlsAdmitidas[$prueba];
        
        // Quitar guiones para facilitar búsquedas tipo "prueba-3-angel" => "prueba3angel"
        $codigoLimpiado = strtoupper(trim(str_replace('-', '', $codigoSanitizado)));

        $esValido = false;

        // 1. Acepta coincidencia exacta del código viejo
        if (hash_equals($claveEsperada, $codigoLimpiado)) {
            $esValido = true;
        }

        // 2. O busca en base de datos si es una URL generada dinámicamente
        if (!$esValido) {
            $qrEnBD = \App\Models\Qr::where('url', $codigoSanitizado)->first();
            
            // Si no coincide la URL exacta, intentamos buscar si el slug de la URL coincide en la BD
            if (!$qrEnBD) {
                $pos = strrpos(strtolower($codigoSanitizado), '/qr/');
                if ($pos !== false) {
                    $slug = ltrim(substr($codigoSanitizado, $pos + 4), '/');
                    $qrEnBD = \App\Models\Qr::where('url', 'LIKE', '%/qr/' . $slug)->first();
                }
            }

            if ($qrEnBD) {
                // El QR existe en la BD. Verificar la fase asignada.
                $faseEsperada = match($prueba) {
                    3 => 'Prueba 3 Angel',
                    4 => 'Prueba 4 Arturo',
                    5 => 'Prueba 5 Eloy',
                    default => ''
                };

                // Normalizamos ambas cadenas para comparar sin fallos de espacios
                if (strtolower(trim($qrEnBD->fase_requerida)) === strtolower(trim($faseEsperada))) {
                    $esValido = true;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este código QR pertenece a otra prueba.',
                    ], 422);
                }
            }
        }

        if (!$esValido) {
            Log::info('QR inválido', [
                'prueba'             => $prueba,
                'codigo_normalizado' => $codigoLimpiado,
                'codigo_original'    => $codigoSanitizado,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Código incorrecto. Inténtalo de nuevo.',
            ], 422);
        }

        // Marcar validación del QR en sesión (sin romper el resto del juego)
        // - `prueba_X_completada`: usado por este validador QR
        // - además, por compatibilidad, marcamos "superada" de la prueba anterior si corresponde
        session([$keyActualCompletada => true]);

        // (Opcional) Persistencia en BBDD si existe un modelo/tabla de progreso:
        // - Puedes integrarlo aquí (GameState/Progress) usando auth()->id()

        return response()->json([
            'success' => true,
            'redirect_url' => $this->redirectUrlParaPrueba($prueba),
        ]);
    }

    /**
     * Determina a qué ruta debe ir el alumno tras validar el QR
     * de cada prueba.
     */
    private function redirectUrlParaPrueba(int $prueba): string
    {
        return match ($prueba) {
            3 => route('juego.maquina-cafe'),
            4 => route('juego.maquina-snacks'),
            5 => route('juego.taquillas'),
            default => route('prueba' . ($prueba + 1)),
        };
    }




    // =====================================================
    // SISTEMA DE QRs (MÓDULO VERSIÓN BBDD DINÁMICA)
    // =====================================================

    public function mostrarGestor()
    {
        $qrs = \App\Models\Qr::orderBy('created_at', 'desc')->get();
        return view('admin.gestionar-qrs', compact('qrs'));
    }

    public function guardarQr(GuardarQrRequest $request)
    {
        $baseUrl = rtrim(config('app.url', 'https://dawscape.fun'), '/');
        // Quitar guiones para conseguir slugs de tipo 'prueba3angel'
        $slug = str_replace('-', '', \Illuminate\Support\Str::slug($request->nombre));
        $url = $baseUrl . '/qr/' . $slug;

        \App\Models\Qr::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fase_requerida' => $request->fase_requerida,
            'url' => $url
        ]);

        return back()->with('success', 'QR "' . $request->nombre . '" generado correctamente.');
    }

    public function eliminarQr($id)
    {
        $qr = \App\Models\Qr::find($id);
        if ($qr) {
            $qr->delete();
            return back()->with('success', 'QR eliminado del sistema.');
        }
        return back()->with('error', 'El QR no existe.');
    }

    public function editarQr(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $qr = \App\Models\Qr::find($id);
        if ($qr) {
            $qr->update(['nombre' => $request->nombre]);
            return back()->with('success', 'Nombre del QR actualizado correctamente.');
        }
        return back()->with('error', 'El QR no existe.');
    }

    public function escanearQR($identificador)
    {
        // 1. Autenticación: verificar que existe sesión activa
        if (!session()->has('email')) {
            return redirect('/')
                ->with('error', 'Inicia sesión con tu equipo para ver esta pista.');
        }

        $identificadorMap = [
            'prueba3angel' => 'Prueba 3 Angel',
            'prueba4arturo' => 'Prueba 4 Arturo',
            'prueba5eloy' => 'Prueba 5 Eloy'
        ];

        $identificadorNormalizado = strtolower(trim($identificador));

        // Buscamos el QR en la BBDD
        $qr = \App\Models\Qr::where('url', 'LIKE', '%/qr/' . $identificador)->first();

        // Si existe un mapeo específico requerido o el QR lo dicta
        $faseQrRequerida = null;
        if (isset($identificadorMap[$identificadorNormalizado])) {
            $faseQrRequerida = $identificadorMap[$identificadorNormalizado];
        } elseif ($qr && !empty($qr->fase_requerida)) {
            $faseQrRequerida = $qr->fase_requerida;
        } else {
            // Que no exista ningún caso donde un QR genérico dé acceso a cualquier prueba
            abort(404, 'Código QR no reconocido o sin prueba asignada.');
        }

        // Recuperamos la partida
        $partida = $this->timerService->obtenerPartidaActiva();

        if (!$partida) {
            return redirect('/')->with('error', 'No se encontró una partida activa para este equipo.');
        }

        $fasesOrdenadas = [
            'prueba 1 mario' => 1,
            'prueba 2 yoseth' => 2,
            'prueba 3 angel' => 3,
            'prueba 4 arturo' => 4,
            'prueba 5 eloy' => 5,
            'prueba 6 migue' => 6,
            'prueba 7 dinamica' => 7,
            'finalizado' => 8
        ];

        $faseActualNormalizada = trim(strtolower($partida->fase_actual ?? ''));
        $faseQrNormalizada = trim(strtolower($faseQrRequerida));

        $nivelActual = $fasesOrdenadas[$faseActualNormalizada] ?? 0;
        $nivelQr = $fasesOrdenadas[$faseQrNormalizada] ?? 0;

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();

        // 2 y 3. Fase correcta y QR correcto
        if ($nivelActual < $nivelQr) {
            return view('qr.error-fase', [
                'mensaje' => '¡Eh, listillos! Aún no estáis en esta fase. Resolved la prueba actual primero.',
                'tiempo_transcurrido' => $tiempo_transcurrido
            ]);
        } elseif ($nivelActual > $nivelQr) {
            return view('qr.error-fase', [
                'mensaje' => 'Ya habéis superado esta fase. Continuad con vuestra prueba actual.',
                'tiempo_transcurrido' => $tiempo_transcurrido
            ]);
        }

        // ── Si el tiempo expiró, cerramos la prueba ──
        if ($this->timerService->verificarTimeout()) {
            return redirect('/final')->with('timeout', true);
        }

        // ── ÉXITO: renderizar el reto QR ──
        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();
        $titulo_prueba = $qr->nombre ?? $faseQrRequerida;

        $vistaEspecial = match(true) {
            // Por fase_requerida (el campo más fiable)
            str_contains($faseQrNormalizada, 'angel')  || str_contains($faseQrNormalizada, 'prueba 3') => 'qr.maquina-cafe',
            str_contains($faseQrNormalizada, 'arturo') || str_contains($faseQrNormalizada, 'prueba 4') => 'qr.maquina-snacks',
            str_contains($faseQrNormalizada, 'eloy')   || str_contains($faseQrNormalizada, 'prueba 5') => 'qr.taquillas',
            // Fallback por slug
            str_contains($identificadorNormalizado, 'cafe')    => 'qr.maquina-cafe',
            str_contains($identificadorNormalizado, 'snacks')  => 'qr.maquina-snacks',
            str_contains($identificadorNormalizado, 'taquilla')=> 'qr.taquillas',
            default => 'qr.maquina-cafe'
        };

        return view($vistaEspecial, compact('tiempo_transcurrido', 'titulo_prueba', 'identificador'));
    }

    // =====================================================
    // SISTEMA DE QRS DEL CÓDICE OCULTO (Prueba 7 Dinámica)
    // URLs fijas: /qr/codice-0 ... /qr/codice-9
    // =====================================================

    /**
     * Base de datos de acértijos por dígito.
     * Ref: JuegoController::DIGITOS_CODICE (mantener sincronizado).
     */
    private const ACERTIJOS_CODICE = [
        0 => 'No valgo absolutamente nada si me pones a la izquierda, pero multiplico tu riqueza si me pones a la derecha. Fui el último dígito en inventarse, nacido en la India para representar la nada y el vacío absoluto.',
        1 => 'Soy el único número cuyo cuadrado, cubo y raíz son exactamente idénticos a mí mismo. En la tabla periódica, corono al elemento más ligero y abundante de todo el universo.',
        2 => 'En informática, soy la base fundamental de todo el lenguaje que entienden las máquinas. Soy la excepción a la regla: el único número par que tiene el honor de ser primo.',
        3 => 'Newton necesitó exactamente este número de leyes para explicar el movimiento del universo. Si quieres definir un plano geométrico perfecto en el espacio sin que cojee, me necesitas estrictamente.',
        4 => 'Lato sin descanso en tu pecho gracias a mis cavidades anatómicas. En la genética, soy el total de letras químicas (A, C, G, T) que escriben el libro de la vida.',
        5 => 'En la geometría clásica, represento el total de sólidos platónicos perfectos que existen. En el emblema de los Juegos Olímpicos, doy forma a los anillos que representan las partes habitadas del mundo.',
        6 => 'Soy el primer número perfecto, la suma de mis divisores exactos (1, 2 y 3) te dará mi valor. En química, soy el cimiento de la vida, el número atómico del carbono.',
        7 => 'Químicamente hablando, soy la neutralidad perfecta, el pH del agua pura. Anatómicamente, soy el número de vértebras cervicales que comparte un humano con una jirafa.',
        8 => 'En el mundo digital, formo la unidad básica de memoria (el byte) cuando agrupo a mis pequeños soldados (los bits). Soy el número de planetas reconocidos oficialmente en nuestro sistema solar.',
        9 => 'Tengo una maldición: multiplícame por el entero que quieras; si sumas las cifras del resultado, siempre volverás a mí. Soy el total de círculos del Infierno de Dante.',
    ];

    public function escanearCodicedigito($digito)
    {
        // Forzar entero y validar rango 0-9
        $digito = (int) $digito;
        if ($digito < 0 || $digito > 9) {
            abort(404, 'Código QR no válido.');
        }

        // Guard: requiere sesión activa de equipo
        if (!session()->has('email')) {
            return redirect('/')
                ->with('error', 'Inicia sesión con tu equipo para ver esta pista.');
        }

        // Verificar que el equipo está en la fase correcta
        $partida = $this->timerService->obtenerPartidaActiva();

        if (!$partida) {
            return redirect('/')->with('error', 'No se encontró una partida activa para este equipo.');
        }

        $fasesOrdenadas = [
            'prueba 1 mario' => 1,
            'prueba 2 yoseth' => 2,
            'prueba 3 angel' => 3,
            'prueba 4 arturo' => 4,
            'prueba 5 eloy' => 5,
            'prueba 6 migue' => 6,
            'prueba 7 dinamica' => 7,
            'finalizado' => 8
        ];

        $faseActualNormalizada = trim(strtolower($partida->fase_actual ?? ''));
        $nivelActual = $fasesOrdenadas[$faseActualNormalizada] ?? 0;
        $nivelQr = $fasesOrdenadas['prueba 7 dinamica'];

        $tiempo_transcurrido = $this->timerService->obtenerTiempoTranscurrido();

        if ($nivelActual < $nivelQr) {
            return view('qr.error-fase', [
                'mensaje' => '¡Eh, listillos! Aún no estáis en esta fase. Resolved la prueba actual primero.',
                'tiempo_transcurrido' => $tiempo_transcurrido
            ]);
        } elseif ($nivelActual > $nivelQr) {
            return view('qr.error-fase', [
                'mensaje' => 'Ya habéis superado esta fase. Continuad con vuestra prueba actual.',
                'tiempo_transcurrido' => $tiempo_transcurrido
            ]);
        }

        // Timeout check
        if ($this->timerService->verificarTimeout()) {
            return redirect('/final')->with('timeout', true);
        }

        $acertijo = self::ACERTIJOS_CODICE[$digito];

        return view('qr.codice-digito', compact('digito', 'acertijo'));
    }
}
