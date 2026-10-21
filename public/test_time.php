<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$partida = \App\Models\Partida::latest()->first();
if ($partida) {
    echo "Inicio DB: " . $partida->inicio_juego . "\n";
    $inicio = \Carbon\Carbon::parse($partida->inicio_juego);
    echo "Inicio parsed: " . $inicio . "\n";
    echo "Now(): " . now() . "\n";
    echo "Diff (Admin style): " . abs(now()->diffInSeconds($inicio)) . "\n";
    
    // Simulate GameTimerService logic exactly
    $ahora = \Carbon\Carbon::now();
    $tiempoTotal = (int) abs($ahora->diffInSeconds($inicio));
    echo "Diff (GameTimerService style): " . $tiempoTotal . "\n";
} else {
    echo "NO PARTIDAS";
}
