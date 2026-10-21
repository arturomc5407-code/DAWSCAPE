<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Partida;
use App\Models\Inscripcion;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    protected $statsService;

    public function __construct(\App\Services\AdminStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function adminPanel()
    {
        // 1. Obtener Top 5
        $top5 = $this->statsService->getTop5();

        // 2. Obtener la lista general
        $partidas = $this->statsService->getPartidasOrdered();

        // 3. Obtener Métricas (Ahora optimizadas desde BBDD)
        $metrics = $this->statsService->getMetrics();
        $totalEquipos =      $metrics['total'];
        $equiposFinalizados =$metrics['finalizados'];
        $equiposActivos =    $metrics['activos'];
        $equiposTimeout =    $metrics['timeout'];
        $promedioSegundos =  $metrics['promedio_sec'];

        // 4. Obtener Chart Data
        $chartData = $this->statsService->getChartData();

        // 5. NUEVO: Obtener Inscripciones (equipos que se registraron pero no han comenzado)
        $emailsConPartida = Partida::pluck('email')->toArray();
        $inscripcionesPendientes = Inscripcion::whereNotIn('correo', $emailsConPartida)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalInscritos = Inscripcion::count();
        $inscritosNoIniciados = $inscripcionesPendientes->count();

        // 6. Autodescubrimiento de rutas de pruebas para el menú Admin
        $rutasPruebas = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->filter(function ($route) {
                return str_starts_with($route->getName(), 'admin.prueba.');
            })
            ->map(function ($route) {
                $nombreSlug = str_replace('admin.prueba.', '', $route->getName());
                $nombreLegible = ucfirst(preg_replace('/([a-zA-Z]+)(\d+)(.*)/', '$1 $2 $3', $nombreSlug));
                return [
                    'url' => url('/') . '/' . ltrim($route->uri(), '/'),
                    'nombre' => 'Test: ' . $nombreLegible
                ];
            })
            ->values();

        return view('admin.dashboard', compact(
            'partidas', 'top5', 'rutasPruebas',
            'totalEquipos', 'equiposFinalizados', 'equiposActivos', 'equiposTimeout', 'chartData',
            'inscripcionesPendientes', 'totalInscritos', 'inscritosNoIniciados'
        ))->with('tiempoMedioSegundos', $promedioSegundos);
    }

    public function eliminarEquipo($uuid)
    {
        $partida = Partida::where('uuid', $uuid)->first();
        if ($partida) {
            $partida->delete();
            return back()->with('success', 'Equipo eliminado correctamente del sistema.');
        }
        return back()->with('error', 'El equipo no existe.');
    }

    public function mostrarLogs($uuid)
    {
        $partida = Partida::with(['logs' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])->where('uuid', $uuid)->firstOrFail();

        return view('admin.logs', compact('partida'));
    }

    public function exportarTop20PDF()
    {
        $top20 = Partida::where('fase_actual', config('fases.fases.finalizado.db'))
            ->orderBy('tiempo_segundos', 'asc')
            ->limit(20)
            ->get();

        $pdf = Pdf::loadView('ranking-pdf', compact('top20'));
        return $pdf->download('Ranking_Top_20_DAWSCAPE.pdf');
    }




    public function analitica()
    {
        // 1. Tiempo medio invertido por prueba (de HitoFase)
        $bottlenecks = $this->statsService->getBottlenecks();

        // 2. Total de fallos por prueba (acciones de tipo 'fallo' en log_juegos)
        $fallosPorPrueba = \App\Models\LogJuego::where('accion', 'like', '%fallo%')
            ->orWhere('accion', 'like', '%error%')
            ->orWhere('accion', 'like', '%incorrec%')
            ->select('fase', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total_fallos'))
            ->groupBy('fase')
            ->orderBy('fase')
            ->get();

        // 3. Pistas usadas agrupadas por prueba
        $pistasPorPrueba = \App\Models\PistaUsada::select('prueba_numero', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total_pistas'))
            ->groupBy('prueba_numero')
            ->orderBy('prueba_numero')
            ->get();

        // Autodescubrimiento de rutas de pruebas para el menú Admin sidebar
        $rutasPruebas = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->filter(fn($route) => str_starts_with($route->getName() ?? '', 'admin.prueba.'))
            ->map(function ($route) {
                $nombreSlug = str_replace('admin.prueba.', '', $route->getName());
                $nombreLegible = ucfirst(preg_replace('/([a-zA-Z]+)(\d+)(.*)/', '$1 $2 $3', $nombreSlug));
                return ['url' => url('/') . '/' . ltrim($route->uri(), '/'), 'nombre' => 'Test: ' . $nombreLegible];
            })->values();

        return view('admin.analitica', compact('bottlenecks', 'fallosPorPrueba', 'pistasPorPrueba', 'rutasPruebas'));
    }

    public function apiStats()
    {
        $partidas = $this->statsService->getPartidasOrdered();
        $metrics = $this->statsService->getMetrics();
        $chartData = $this->statsService->getChartData();
        $bottlenecks = $this->statsService->getBottlenecks();
        $pistasCountByPartida = \App\Models\PistaUsada::select('partida_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('partida_id')
            ->pluck('total', 'partida_id');

        // Limpieza drástica del método, delegando a la capa Controller solo el Response JSON.
        return response()->json([
            'metrics' => [
                'total' =>       $metrics['total'],
                'finalizados' => $metrics['finalizados'],
                'activos' =>     $metrics['activos'],
                'promedio' =>    $metrics['promedio']
            ],
            'chart' => $chartData,
            'bottlenecks' => $bottlenecks,
            'partidas' => $partidas->map(function($p) use ($pistasCountByPartida) {
                $segsLive = 0;
                if ($p->fase_actual !== config('fases.fases.finalizado.db') && $p->fase_actual !== config('fases.fases.timeout.db') && $p->inicio_juego) {
                    $segsLive = abs(now()->diffInSeconds(\Carbon\Carbon::parse($p->inicio_juego)));
                }
                $pistasCount = (int) ($pistasCountByPartida[$p->id] ?? 0);
                return [
                    'uuid' => $p->uuid,
                    'id' => str_pad($p->id, 3, '0', STR_PAD_LEFT),
                    'nombre_equipo' => $p->nombre_equipo,
                    'email' => $p->email,
                    'clase' => $p->clase,
                    'fase_actual' => $p->fase_actual,
                    'tiempo_segundos' => (int)$p->tiempo_segundos,
                    'tiempo_live' => $segsLive,
                    'color_hex' => $p->color_hex,
                    'icono' => $p->icono,
                    'logros' => $p->logros ?? [],
                    'avatar' => strtoupper(substr($p->nombre_equipo, 0, 1)),
                    'pistas_count' => $pistasCount,
                    'codice' => $p->combinacion_codice
                        ? implode('-', $p->combinacion_codice)
                        : null
                ];
            })
        ]);
    }

    public function apiInscripciones()
    {
        $emailsConPartida = Partida::pluck('email')->toArray();
        $inscripcionesPendientes = Inscripcion::whereNotIn('correo', $emailsConPartida)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalInscritos = Inscripcion::count();
        $inscritosNoIniciados = $inscripcionesPendientes->count();

        return response()->json([
            'total_inscritos' => $totalInscritos,
            'no_iniciados' => $inscritosNoIniciados,
            'iniciados' => $totalInscritos - $inscritosNoIniciados,
            'inscripciones_pendientes' => $inscripcionesPendientes->map(function($insc) {
                return [
                    'id' => $insc->id,
                    'nombre_equipo' => $insc->nombre_equipo,
                    'correo' => $insc->correo,
                    'clase' => $insc->clase,
                    'avatar' => strtoupper(substr($insc->nombre_equipo, 0, 1)),
                    'tiempo_inscripcion' => $insc->created_at->diffForHumans(),
                    'created_at' => $insc->created_at
                ];
            })
        ]);
    }

    // ==========================================
    // MÓDULO: CREADOR DINÁMICO DE PRUEBAS
    // ==========================================
    public function eliminarInscripcion($id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $nombreEquipo = $inscripcion->nombre_equipo;
        $inscripcion->delete();

        return back()->with('success', "Inscripción de '" . $nombreEquipo . "' eliminada correctamente.");
    }

    // ==========================================
    // MÓDULO: CREADOR DINÁMICO DE PRUEBAS
    // ==========================================
    public function gestionarPreguntas()
    {
        $preguntas = \App\Models\PreguntaDinamica::orderBy('created_at', 'desc')->get();
        return view('admin.gestionar-preguntas', compact('preguntas'));
    }

    public function guardarPregunta(Request $request)
    {
        $request->validate([
            'enunciado' => 'required|string',
            'respuesta_correcta' => 'required|string|max:255',
            'respuestas_alternativas' => 'nullable|string'
        ]);

        $alternativas = [];
        if ($request->filled('respuestas_alternativas')) {
            $alternativas = array_values(array_filter(array_map('trim', explode(',', $request->respuestas_alternativas))));
        }

        \App\Models\PreguntaDinamica::create([
            'enunciado' => $request->enunciado,
            'respuesta_correcta' => $request->respuesta_correcta,
            'respuestas_alternativas' => $alternativas,
            'activa' => true
        ]);

        return back()->with('success', 'Pregunta guardada correctamente.');
    }

    public function editarPregunta($id)
    {
        $pregunta = \App\Models\PreguntaDinamica::findOrFail($id);
        return view('admin.editar-pregunta', compact('pregunta'));
    }

    public function actualizarPregunta(Request $request, $id)
    {
        $request->validate([
            'enunciado' => 'required|string',
            'respuesta_correcta' => 'required|string|max:255',
            'respuestas_alternativas' => 'nullable|string'
        ]);

        $alternativas = [];
        if ($request->filled('respuestas_alternativas')) {
            $alternativas = array_values(array_filter(array_map('trim', explode(',', $request->respuestas_alternativas))));
        }

        $pregunta = \App\Models\PreguntaDinamica::findOrFail($id);
        $pregunta->enunciado = $request->enunciado;
        $pregunta->respuesta_correcta = $request->respuesta_correcta;
        $pregunta->respuestas_alternativas = $alternativas;
        $pregunta->save();

        return redirect()->route('admin.preguntas')->with('success', 'Pregunta actualizada correctamente.');
    }

    public function togglePregunta($id)
    {
        $pregunta = \App\Models\PreguntaDinamica::findOrFail($id);
        $pregunta->activa = !$pregunta->activa;
        $pregunta->save();

        $estado = $pregunta->activa ? 'activada' : 'desactivada';
        return back()->with('success', "Pregunta $estado exitosamente.");
    }

    public function eliminarPregunta($id)
    {
        $pregunta = \App\Models\PreguntaDinamica::findOrFail($id);
        $pregunta->delete();

        return back()->with('success', 'Pregunta eliminada del sistema.');
    }

    // ==========================================
    // MÓDULO: SYSTEM HEALTH MONITOR
    // ==========================================
    public function systemHealth()
    {
        // Métricas de memoria PHP
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = ini_get('memory_limit');

        // Versiones
        $laravelVersion = app()->version();
        $phpVersion = phpversion();

        // Registros en tablas principales
        // Mapa: nombre visible => nombre real de la tabla en BD
        $tableMap = [
            'Partidas' => 'partidas',
            'Inscripciones' => 'inscripcions',
            'Logs de Juego' => 'log_juegos',
            'Pistas Usadas' => 'pistas_usadas',
            'Preguntas Dinámicas' => 'pregunta_dinamicas',
            'QRs' => 'qrs',
            'Usuarios' => 'users',
            'Hitos de Fase' => 'hitos_fases',
        ];

        $tableCounts = [
            'Partidas' => Partida::count(),
            'Inscripciones' => Inscripcion::count(),
            'Logs de Juego' => \App\Models\LogJuego::count(),
            'Pistas Usadas' => \App\Models\PistaUsada::count(),
            'Preguntas Dinámicas' => \App\Models\PreguntaDinamica::count(),
            'QRs' => \App\Models\Qr::count(),
            'Usuarios' => \App\Models\User::count(),
            'Hitos de Fase' => \App\Models\HitoFase::count(),
        ];
        $totalRecords = array_sum($tableCounts);

        // Tamaño de la base de datos (MySQL)
        $dbSize = 0;
        try {
            $dbName = config('database.connections.mysql.database');
            $result = \Illuminate\Support\Facades\DB::select("
                SELECT ROUND(SUM(data_length + index_length), 0) AS size
                FROM information_schema.tables
                WHERE table_schema = ?
            ", [$dbName]);
            $dbSize = $result[0]->size ?? 0;
        } catch (\Exception $e) {
            $dbSize = 0;
        }

        // Estado de la BD
        $dbOnline = false;
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $dbOnline = true;
        } catch (\Exception $e) {
            $dbOnline = false;
        }

        // Uptime del servidor PHP (tiempo desde inicio del proceso)
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';

        // Extensiones PHP cargadas
        $extensions = get_loaded_extensions();
        $criticalExtensions = ['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'curl', 'json', 'fileinfo'];

        // Espacio en disco
        $diskFree = disk_free_space(base_path());
        $diskTotal = disk_total_space(base_path());

        // Config de caché y sesión
        $cacheDriver = config('cache.default');
        $sessionDriver = config('session.driver');
        $queueDriver = config('queue.default');

        // Autodescubrimiento de rutas para sidebar
        $rutasPruebas = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->filter(fn($route) => str_starts_with($route->getName() ?? '', 'admin.prueba.'))
            ->map(function ($route) {
                $nombreSlug = str_replace('admin.prueba.', '', $route->getName());
                $nombreLegible = ucfirst(preg_replace('/([a-zA-Z]+)(\d+)(.*)/', '$1 $2 $3', $nombreSlug));
                return ['url' => url('/') . '/' . ltrim($route->uri(), '/'), 'nombre' => 'Test: ' . $nombreLegible];
            })->values();

        return view('admin.system-health', compact(
            'memoryUsage', 'memoryPeak', 'memoryLimit',
            'laravelVersion', 'phpVersion',
            'tableCounts', 'tableMap', 'totalRecords', 'dbSize', 'dbOnline',
            'serverSoftware', 'extensions', 'criticalExtensions',
            'diskFree', 'diskTotal',
            'cacheDriver', 'sessionDriver', 'queueDriver',
            'rutasPruebas'
        ));
    }

    /**
     * Visor de datos de una tabla (solo lectura).
     */
    public function showTable(string $table)
    {
        $allowedTables = [
            'partidas', 'inscripcions', 'log_juegos', 'pistas_usadas',
            'pregunta_dinamicas', 'qrs', 'users', 'hitos_fases',
        ];

        if (!in_array($table, $allowedTables, true) || !Schema::hasTable($table)) {
            abort(404);
        }

        $datos = DB::table($table)->paginate(50);

        $rutasPruebas = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->filter(fn($route) => str_starts_with($route->getName() ?? '', 'admin.prueba.'))
            ->map(function ($route) {
                $nombreSlug = str_replace('admin.prueba.', '', $route->getName());
                $nombreLegible = ucfirst(preg_replace('/([a-zA-Z]+)(\d+)(.*)/', '$1 $2 $3', $nombreSlug));
                return ['url' => url('/') . '/' . ltrim($route->uri(), '/'), 'nombre' => 'Test: ' . $nombreLegible];
            })->values();

        return view('admin.database', compact('table', 'datos', 'rutasPruebas'));
    }

    /**
     * Live Feed & Control — Sala de control en tiempo real.
     */
    public function liveFeed()
    {
        $rutasPruebas = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->filter(fn($route) => str_starts_with($route->getName() ?? '', 'admin.prueba.'))
            ->map(function ($route) {
                $nombreSlug = str_replace('admin.prueba.', '', $route->getName());
                $nombreLegible = ucfirst(preg_replace('/([a-zA-Z]+)(\d+)(.*)/', '$1 $2 $3', $nombreSlug));
                return ['url' => url('/') . '/' . ltrim($route->uri(), '/'), 'nombre' => 'Test: ' . $nombreLegible];
            })->values();

        $equiposActivos = Partida::whereNotIn('fase_actual', [
                config('fases.fases.finalizado.db'),
                config('fases.fases.timeout.db'),
            ])
            ->whereNotNull('inicio_juego')
            ->orderBy('inicio_juego', 'asc')
            ->get();

        return view('admin.live-feed', compact('rutasPruebas', 'equiposActivos'));
    }

    /**
     * API: Equipos activos en tiempo real (polling).
     */
    public function apiEquiposActivos()
    {
        $equipos = Partida::whereNotIn('fase_actual', [
                config('fases.fases.finalizado.db'),
                config('fases.fases.timeout.db'),
            ])
            ->whereNotNull('inicio_juego')
            ->orderBy('inicio_juego', 'asc')
            ->get()
            ->map(function ($p) {
                $inicio = \Carbon\Carbon::parse($p->inicio_juego);
                $ahora = \Carbon\Carbon::now();

                $tiempoTotal = (int) abs($ahora->diffInSeconds($inicio));
                $segundosDesdePausa = 0;
                if ($p->estado === 'pausado' && $p->ultima_pausa_at) {
                    $segundosDesdePausa = (int) abs($ahora->diffInSeconds(\Carbon\Carbon::parse($p->ultima_pausa_at)));
                }
                $elapsed = max(0, $tiempoTotal - ($p->tiempo_pausado_segundos ?? 0) - $segundosDesdePausa) + ($p->penalizacion_segundos ?? 0);

                return [
                    'uuid' => $p->uuid,
                    'nombre_equipo' => $p->nombre_equipo,
                    'clase' => $p->clase,
                    'fase_actual' => $p->fase_actual,
                    'penalizacion_segundos' => $p->penalizacion_segundos ?? 0,
                    'tiempo_elapsed' => $elapsed,
                    'estado' => $p->estado,
                    'color_hex' => $p->color_hex,
                    'icono' => $p->icono,
                ];
            });

        return response()->json($equipos);
    }

    /**
     * Ajustar tiempo de un equipo (penalización/bonus) desde el Live Feed.
     */
    public function ajustarTiempo(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string|exists:partidas,uuid',
            'segundos' => 'required|integer',
        ]);

        $partida = Partida::where('uuid', $request->uuid)->firstOrFail();

        $nuevaPenalizacion = max(0, ($partida->penalizacion_segundos ?? 0) + $request->segundos);
        $partida->update(['penalizacion_segundos' => $nuevaPenalizacion]);

        $tipo = $request->segundos > 0 ? 'penalizacion' : 'bonus';

        // Log de la acción
        \App\Models\LogJuego::create([
            'partida_id' => $partida->id,
            'fase' => $partida->fase_actual,
            'accion' => $tipo === 'penalizacion' ? 'PENALIZACION_MANUAL' : 'BONUS_MANUAL',
            'detalles' => [
                'segundos' => $request->segundos,
                'admin' => auth()->user()->name,
                'penalizacion_total' => $nuevaPenalizacion,
            ],
        ]);

        // Enviar evento al equipo para que vean el cambio
        event(new \App\Events\TiempoAjustado(
            $partida->uuid,
            $request->segundos,
            $tipo,
            $nuevaPenalizacion
        ));

        // Notificar al dashboard admin
        event(new \App\Events\AdminDashboardUpdated());

        return response()->json([
            'ok' => true,
            'penalizacion_total' => $nuevaPenalizacion,
            'equipo' => $partida->nombre_equipo,
        ]);
    }
}
