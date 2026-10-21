<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Top10Controller;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\ValoracionController;

// ==========================================
// ZONA PUBLICA - ESCAPE ROOM (ALUMNOS)
// ==========================================

Route::get('/', function () {
    return view('inicio');
})->name('inicio');

Route::get('/legal', function () {
    return view('aviso-legal');
})->name('legal');

// ==========================================
// INSCRIPCIÓN PREVIA DE EQUIPOS
// ==========================================
Route::get('/inscripcion', [InscripcionController::class, 'mostrar'])->name('inscripcion.mostrar');
Route::post('/inscripcion', [InscripcionController::class, 'guardar'])->name('inscripcion.guardar');
Route::get('/inscripcion/exito/{token}', [InscripcionController::class, 'exito'])->name('inscripcion.exito');
Route::get('/inscripcion/qr/{token}', [InscripcionController::class, 'descargarQr'])->name('inscripcion.qr');
Route::get('/inscripcion/validar/{token}', [InscripcionController::class, 'validarQr'])->name('inscripcion.validar');

Route::post('/empezar', [JuegoController::class, 'empezarPartida']);

// Grupo protegido para las fases del juego (Asegura progreso lineal)
Route::middleware(['check.fase'])->group(function () {
    Route::get('/prueba1', [JuegoController::class, 'mostrarPrueba1'])->name('prueba1');
    Route::post('/prueba1', [JuegoController::class, 'validarPrueba1']);

    Route::get('/prueba2', [JuegoController::class, 'mostrarPrueba2'])->name('prueba2');
    Route::post('/prueba2', [JuegoController::class, 'validarPrueba2']);

    Route::get('/prueba3', [JuegoController::class, 'mostrarPrueba3'])->name('prueba3');
    Route::post('/prueba3', [JuegoController::class, 'validarPrueba3']);

    Route::get('/prueba4', [JuegoController::class, 'mostrarPrueba4'])->name('prueba4');
    Route::post('/prueba4', [JuegoController::class, 'validarPrueba4']);

    Route::get('/prueba5', [JuegoController::class, 'mostrarPrueba5'])->name('prueba5');
    Route::post('/prueba5', [JuegoController::class, 'validarPrueba5']);

    Route::get('/prueba6', [JuegoController::class, 'mostrarPrueba6'])->name('prueba6');
    Route::post('/prueba6', [JuegoController::class, 'validarPrueba6']);
    Route::post('/prueba6/penalizar', [JuegoController::class, 'penalizarPrueba6']);

    Route::get('/prueba7', [JuegoController::class, 'mostrarPrueba7'])->name('prueba7');
    Route::post('/prueba7', [JuegoController::class, 'validarPrueba7']);

    Route::get('/prueba8', [JuegoController::class, 'mostrarPrueba8'])->name('prueba8');
    Route::post('/prueba8', [JuegoController::class, 'validarPrueba8']);

    Route::get('/prueba9', [JuegoController::class, 'mostrarPrueba9'])->name('prueba9');
    Route::post('/prueba9', [JuegoController::class, 'validarPrueba9'])->middleware('throttle:20,1');

    Route::get('/final', [JuegoController::class, 'mostrarFinal'])->name('final');

    // Sistema de Pistas/Ayuda
    Route::post('/pista', [JuegoController::class, 'usarPista'])->name('pista.usar');
});

// ==========================================
// QRs DEL CÓDICE OCULTO (Prueba 7 Dinámica)
// Deben ir ANTES del comodín /qr/{id}
// ==========================================
foreach (range(0, 9) as $digito) {
    Route::get('/qr/codice-' . $digito, [QrController::class, 'escanearCodicedigito'])
        ->defaults('digito', $digito)
        ->name('qr.codice.' . $digito);
}

// Enrutamiento Inteligente de Escaneo QR
Route::get('/qr/{id}', [QrController::class, 'escanearQr'])->name('qr.scan');

// Validación de códigos QR (texto plano secreto) para Pruebas 3-5
Route::post('/validar-codigo-qr', [QrController::class, 'validar'])
    // Para ALUMNOS: usamos la sesión de juego (middleware check.fase),
    // no el login de profesor (auth). Mantiene rate limiting.
    ->middleware(['web', 'check.fase', 'throttle:5,1'])
    ->name('qr.validar');


// Vistas de juego para las máquinas físicas (alumnos, sin panel admin)
Route::middleware(['check.fase'])->group(function () {
    Route::get('/juego/maquina-cafe', function () {
        $timerService = app(\App\Services\GameTimerService::class);
        $tiempo_transcurrido = $timerService->obtenerTiempoTranscurrido();
        $titulo_prueba = 'Máquina de Café';
        $identificador = 'fase3';

        return view('qr.maquina-cafe', compact('tiempo_transcurrido', 'titulo_prueba', 'identificador'));
    })->name('juego.maquina-cafe');

    Route::get('/juego/maquina-snacks', function () {
        $timerService = app(\App\Services\GameTimerService::class);
        $tiempo_transcurrido = $timerService->obtenerTiempoTranscurrido();
        $titulo_prueba = 'Máquina Oasis';
        $identificador = 'fase4';

        return view('qr.maquina-snacks', compact('tiempo_transcurrido', 'titulo_prueba', 'identificador'));
    })->name('juego.maquina-snacks');

    Route::get('/juego/taquillas', function () {
        $timerService = app(\App\Services\GameTimerService::class);
        $tiempo_transcurrido = $timerService->obtenerTiempoTranscurrido();
        $titulo_prueba = 'Protocolo Espejo';
        $identificador = 'fase5';

        return view('qr.taquillas', compact('tiempo_transcurrido', 'titulo_prueba', 'identificador'));
    })->name('juego.taquillas');
});

// ==========================================
// ZONA PRIVADA - PANEL DE PROFESORES
// ==========================================

// Envolvemos todo el Dashboard y gestión en el Middleware nativo Auth (Breeze)
Route::middleware(['auth', 'verified'])->group(function () {

    // El dashboard principal será nuestra vista /admin
    Route::get('/dashboard', [AdminController::class, 'adminPanel'])->name('dashboard');
    Route::get('/admin', function() { return redirect('/dashboard'); }); // Alias por costumbre

    // Analítica de Pruebas
    Route::get('/admin/analitica', [AdminController::class, 'analitica'])->name('admin.analitica');

    // Gestión de Partidas y Ticker Live
    Route::get('/admin/api/stats', [AdminController::class, 'apiStats']);
    Route::get('/admin/api/inscripciones', [AdminController::class, 'apiInscripciones']);
    Route::delete('/admin/inscripcion/{id}', [AdminController::class, 'eliminarInscripcion']);
    Route::delete('/admin/equipo/{uuid}', [AdminController::class, 'eliminarEquipo']);
    Route::get('/admin/equipo/{uuid}/logs', [AdminController::class, 'mostrarLogs'])->name('admin.equipo.logs');
    Route::get('/admin/exportar-top20', [AdminController::class, 'exportarTop20PDF']);

    // System Health
    Route::get('/admin/system', [AdminController::class, 'systemHealth'])->name('admin.system');

    // Visor de datos de tablas (solo lectura)
    Route::get('/admin/database/{table}', [AdminController::class, 'showTable'])->name('admin.database.show');

    // Live Feed & Control (Sala de control en tiempo real)
    Route::get('/admin/live', [AdminController::class, 'liveFeed'])->name('admin.live');
    Route::post('/admin/live/ajustar-tiempo', [AdminController::class, 'ajustarTiempo'])->name('admin.live.ajustar');
    Route::get('/admin/api/equipos-activos', [AdminController::class, 'apiEquiposActivos'])->name('admin.api.equipos-activos');

    // Gestión de QRs Dinámicos
    Route::get('/admin/qrs', [QrController::class, 'mostrarGestor'])->name('admin.qrs');
    Route::post('/admin/qrs', [QrController::class, 'guardarQr']);
    Route::patch('/admin/qrs/{id}/editar', [QrController::class, 'editarQr']);
    Route::delete('/admin/qrs/{id}', [QrController::class, 'eliminarQr']);

    // Rutas de Testing Directo (Bypass para el profesor)
    //
    // Cada bypass hace upsert de una Partida con email admin-test@pruebaN.test
    // y fase_actual fijada a la prueba concreta, de forma que los validators
    // POST (que leen obtenerPartidaActiva() + fase_actual) encuentren siempre
    // una partida y se pueda completar el flujo end-to-end de cada prueba.
    //
    // Cada entrada al bypass RESETEA la partida admin de esa prueba y limpia
    // las flags de sesión pruebaN_*_superada, para que el test sea repetible
    // y no arrastre estado del intento anterior.
    $adminBypass = function (int $n, array $sessionExtras = [], ?\Closure $after = null) {
        $email = "admin-test@prueba{$n}.test";
        $faseKey = "fases.fases.prueba{$n}";

        $partida = \App\Models\Partida::firstOrNew(['email' => $email]);
        $partida->fill([
            'nombre_equipo'              => 'ADMIN TEST',
            'clase'                      => 'Pruebas',
            'fase_actual'                => config("{$faseKey}.db"),
            'inicio_juego'               => now(),
            'tiempo_segundos'            => 0,
            'tiempo_pausado_segundos'    => 0,
            'ultima_pausa_at'            => null,
            'penalizacion_segundos'      => 0,
            'estado'                     => 'jugando',
            'intentos_codice'            => 0,
            'intentos_prueba9'           => 0,
            'hints_used'                 => false,
            'combinacion_codice'         => null,
            'logros'                     => [],
            'ranking_posicion_bloqueada' => null,
            'color_hex'                  => $partida->color_hex ?: '#00ff88',
            'icono'                      => $partida->icono ?: 'ph-user-focus',
        ])->save();

        // Flags de progresión — marcamos TODAS las pruebas anteriores como
        // superadas para evitar que CheckFase redirija la POST/GET.
        $progresoFlags = [
            'sergio_intro_visto'         => true,
            'prueba1mario_superada'      => $n >= 2,
            'prueba2yoseth_superada'     => $n >= 3,
            'prueba3angel_superada'      => $n >= 4,
            'prueba4arturo_superada'     => $n >= 5,
            'prueba5eloy_superada'       => $n >= 6,
            'prueba6migue_superada'      => $n >= 7,
            'prueba7_superada'           => $n >= 8,
            'prueba8_superada'           => $n >= 9,
        ];

        session(array_merge([
            'email'          => $email,
            'nombre_equipo'  => 'ADMIN TEST',
            'clase'          => 'Pruebas',
        ], $progresoFlags, $sessionExtras));

        // Limpiar flags de superación y estados intermedios de ESTA prueba
        // para que el test sea repetible.
        session()->forget([
            "prueba{$n}mario_superada",
            "prueba{$n}yoseth_superada",
            "prueba{$n}angel_superada",
            "prueba{$n}arturo_superada",
            "prueba{$n}eloy_superada",
            "prueba{$n}migue_superada",
            "prueba{$n}_superada",
            'prueba6_sequence',
            'prueba8_preguntas',
            'prueba9_index',
            'prueba9_letters',
            'prueba9_intentos_fallidos',
        ]);

        // Volver a marcar la prueba N-1 como superada tras el forget (por si
        // el forget tocó la flag propia de la prueba anterior).
        if ($n >= 2) session(['prueba1mario_superada'  => true]);
        if ($n >= 3) session(['prueba2yoseth_superada' => true]);
        if ($n >= 4) session(['prueba3angel_superada'  => true]);
        if ($n >= 5) session(['prueba4arturo_superada' => true]);
        if ($n >= 6) session(['prueba5eloy_superada'   => true]);
        if ($n >= 7) session(['prueba6migue_superada'  => true]);
        if ($n >= 8) session(['prueba7_superada'       => true]);
        if ($n >= 9) session(['prueba8_superada'       => true]);

        if ($after) $after($partida);
        return $partida;
    };

    Route::get('/prueba/prueba1', function () use ($adminBypass) {
        $adminBypass(1);
        return app(\App\Http\Controllers\JuegoController::class)->mostrarPrueba1();
    })->name('admin.prueba.prueba1');

    Route::get('/prueba/prueba2', function () use ($adminBypass) {
        $adminBypass(2);
        return app(\App\Http\Controllers\JuegoController::class)->mostrarPrueba2();
    })->name('admin.prueba.prueba2');

    Route::get('/prueba/prueba3', function () use ($adminBypass) {
        $adminBypass(3, ['prueba_2_completada' => true]);
        return view('qr.maquina-cafe', [
            'tiempo_transcurrido' => 0,
            'titulo_prueba'       => '[ADMIN TEST] Máquina de Café',
            'identificador'       => 'admin-bypass',
        ]);
    })->name('admin.prueba.prueba3');

    Route::get('/prueba/prueba4', function () use ($adminBypass) {
        $adminBypass(4, ['prueba_3_completada' => true]);
        return view('qr.maquina-snacks', [
            'tiempo_transcurrido' => 0,
            'titulo_prueba'       => '[ADMIN TEST] Máquina Oasis',
            'identificador'       => 'admin-bypass',
        ]);
    })->name('admin.prueba.prueba4');

    Route::get('/prueba/prueba5', function () use ($adminBypass) {
        $adminBypass(5, ['prueba_4_completada' => true]);
        return view('qr.taquillas', [
            'tiempo_transcurrido' => 0,
            'titulo_prueba'       => '[ADMIN TEST] Protocolo Espejo',
            'identificador'       => 'admin-bypass',
        ]);
    })->name('admin.prueba.prueba5');

    Route::get('/prueba/prueba6', function () use ($adminBypass) {
        $adminBypass(6);
        return app(\App\Http\Controllers\JuegoController::class)->mostrarPrueba6();
    })->name('admin.prueba.prueba6');

    Route::get('/prueba/prueba7', function () use ($adminBypass) {
        $adminBypass(7);
        return app(\App\Http\Controllers\JuegoController::class)->mostrarPrueba7();
    })->name('admin.prueba.prueba7');

    Route::get('/prueba/prueba8', function () use ($adminBypass) {
        $adminBypass(8);
        return app(\App\Http\Controllers\JuegoController::class)->mostrarPrueba8();
    })->name('admin.prueba.prueba8');

    Route::get('/prueba/prueba9', function () use ($adminBypass) {
        $adminBypass(9, ['prueba_8_completada' => true]);
        return app(\App\Http\Controllers\JuegoController::class)->mostrarPrueba9();
    })->name('admin.prueba.prueba9');

    // Bypass para ver el diploma final de prueba (sin necesidad de haber jugado)
    Route::get('/prueba/final', function () {
        // Crear una partida falsa en memoria (no persistida) para previsualizar el diploma
        $partida = new \App\Models\Partida([
            'email'                => 'admin-test@dawscape.test',
            'nombre_equipo'        => 'EQUIPO TEST',
            'clase'                => '2º DAW',
            'fase_actual'          => config('fases.fases.finalizado.db'),
            'tiempo_segundos'      => 2754, // 45m 54s de ejemplo
            'penalizacion_segundos'=> 60,
        ]);

        // Top 3 real de la BD (puede estar vacío, no importa)
        $top3 = \App\Models\Partida::where('fase_actual', config('fases.fases.finalizado.db'))
            ->orderBy('tiempo_segundos', 'asc')
            ->limit(3)
            ->get();

        // Hitos de ejemplo para mostrar el breakdown de rendimiento
        $hitos = [
            ['fase' => 'Prueba 1 — Mario',        'segundos_invertidos' => 480,  'total_acumulado' => 480],
            ['fase' => 'Prueba 2 — Clúster',       'segundos_invertidos' => 360,  'total_acumulado' => 840],
            ['fase' => 'Prueba 3 — Cafeína',       'segundos_invertidos' => 300,  'total_acumulado' => 1140],
            ['fase' => 'Prueba 4 — Oasis',         'segundos_invertidos' => 270,  'total_acumulado' => 1410],
            ['fase' => 'Prueba 5 — Protocolo Espejo','segundos_invertidos'=> 240, 'total_acumulado' => 1650],
            ['fase' => 'Prueba 6 — Sobrecarga',    'segundos_invertidos' => 320,  'total_acumulado' => 1970],
            ['fase' => 'Prueba 7 — Códice Oculto', 'segundos_invertidos' => 280,  'total_acumulado' => 2250],
            ['fase' => 'Prueba 8 — Cortafuegos',   'segundos_invertidos' => 245,  'total_acumulado' => 2495],
            ['fase' => 'Prueba 9 — Juego de Pistas','segundos_invertidos' => 259, 'total_acumulado' => 2754],
        ];

        return view('final', compact('partida', 'top3', 'hitos'));
    })->name('admin.prueba.final');

    // Gestión de Perfil de Profesor (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // SISTEMA CREADOR DE PRUEBAS: Panel de Administración
    // ==========================================
    Route::get('/preguntas', [AdminController::class, 'gestionarPreguntas'])->name('admin.preguntas');
    Route::post('/preguntas', [AdminController::class, 'guardarPregunta']);
    Route::get('/admin/pregunta/{id}/edit', [AdminController::class, 'editarPregunta'])->name('admin.pregunta.edit');
    Route::put('/admin/pregunta/{id}', [AdminController::class, 'actualizarPregunta'])->name('admin.pregunta.update');
    Route::post('/preguntas/{id}/toggle', [AdminController::class, 'togglePregunta']);
    Route::delete('/preguntas/{id}', [AdminController::class, 'eliminarPregunta']);
});

// ==========================================
// PANTALLA PÚBLICA TOP 10 (LIVE)
// ==========================================
Route::get('/top10', [Top10Controller::class, 'index'])->name('top10');
Route::get('/api/top10', [Top10Controller::class, 'apiData']);
Route::get('/top10/datos', [Top10Controller::class, 'apiData'])->name('top10.datos');

// Rutas de Laravel Auth
require __DIR__.'/auth.php';

Route::post('/valoraciones', [App\Http\Controllers\ValoracionController::class, 'store'])->name('valoraciones.store');
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/valoraciones', [App\Http\Controllers\ValoracionController::class, 'index'])->name('admin.valoraciones');
});
