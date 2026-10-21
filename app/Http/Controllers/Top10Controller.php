<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Top10Controller extends Controller
{
    // Define the sequence of phases and their friendly names for the progress bar
    private array $fases_orden;

    public function __construct()
    {
        $this->fases_orden = collect(config('fases.fases', []))
            ->mapWithKeys(function ($f) {
                return [
                    $f['db'] => [
                        'nombre' => $f['nombre'],
                        'peso' => $f['peso'],
                    ]
                ];
            })
            ->all();
    }

    public function index()
    {
        return view('top10');
    }

    private function formatSeconds(int $seconds): string
    {
        return floor($seconds / 60) . 'm ' . str_pad($seconds % 60, 2, '0', STR_PAD_LEFT) . 's';
    }

    public function apiData()
    {
        Log::info('Top10 datos endpoint llamado');

        try {
            // Get ticker message from cache
            $ticker = Cache::get('top10_ticker', '¡Bienvenidos al Escape Room DAWSCAPE! Que gane el mejor equipo.');

            $terminadasFases = [
                config('fases.fases.finalizado.db'),
                config('fases.fases.timeout.db'),
            ];
            $maxPesoEnJuego = (int) config('fases.max_peso_en_juego', 9);
            $penalizacionPista = (int) config('escape.penalizacion_pista', 480);

            // Solo equipos que empezaron juego y ya tienen fase
            $partidas = Partida::whereNotNull('fase_actual')
                ->whereNotNull('inicio_juego')
                ->get();

            $todasPartidas = $partidas->map(function ($partida) use ($terminadasFases, $maxPesoEnJuego, $penalizacionPista) {
                $penalizacion = (int) ($partida->penalizacion_segundos ?? 0);
                $esTerminada = in_array($partida->fase_actual, $terminadasFases, true);

                if ($esTerminada) {
                    $tiempoTotal = max(0, (int) ($partida->tiempo_segundos ?? 0));
                    $tiempoRanking = max(0, $tiempoTotal - $penalizacion);
                } else {
                    $segundosTranscurridos = (int) abs(now()->diffInSeconds(Carbon::parse($partida->inicio_juego)));
                    $segundosDesdePausa = 0;

                    if (($partida->estado ?? null) === 'pausado' && $partida->ultima_pausa_at) {
                        $segundosDesdePausa = (int) abs(now()->diffInSeconds(Carbon::parse($partida->ultima_pausa_at)));
                    }

                    $tiempoRanking = max(0, $segundosTranscurridos - (int) ($partida->tiempo_pausado_segundos ?? 0) - $segundosDesdePausa);
                    $tiempoTotal = $tiempoRanking + $penalizacion;
                }

                $peso = $this->fases_orden[$partida->fase_actual]['peso'] ?? 0;
                $nombre_prueba = $this->fases_orden[$partida->fase_actual]['nombre'] ?? $partida->fase_actual;
                $progreso = $maxPesoEnJuego > 0 ? min(100, round(($peso / $maxPesoEnJuego) * 100)) : 0;

                $estado = match ($partida->fase_actual) {
                    config('fases.fases.finalizado.db') => 'finalizado',
                    config('fases.fases.timeout.db') => 'timeout',
                    default => 'jugando',
                };

                $pistasUsadas = $penalizacionPista > 0 ? (int) floor($penalizacion / $penalizacionPista) : 0;

                return [
                    'id' => $partida->id,
                    'nombre_equipo' => $partida->nombre_equipo,
                    'fase_actual' => $partida->fase_actual,
                    'nombre_prueba' => $nombre_prueba,
                    'fase_numero' => min($maxPesoEnJuego, max(0, (int) $peso)),
                    'progreso_porcentaje' => $progreso,
                    'tiempo_segundos' => $tiempoRanking,
                    'tiempo_total_segundos' => $tiempoTotal,
                    'tiempo_formateado' => $this->formatSeconds($tiempoRanking),
                    'tiempo_total_formateado' => $this->formatSeconds($tiempoTotal),
                    'peso' => $peso,
                    'estado' => $estado,
                    'hora_fin' => $partida->updated_at ? $partida->updated_at->format('H:i') : '--:--',
                    'color_hex' => $partida->color_hex ?? '#3b82f6',
                    'icono' => $partida->icono ?? 'ph-user-focus',
                    'logros' => $partida->logros ?? [],
                    'pistas_usadas' => $pistasUsadas,
                    'penalizacion_segundos' => $penalizacion,
                    'ranking_posicion_bloqueada' => $partida->ranking_posicion_bloqueada ? (int) $partida->ranking_posicion_bloqueada : null,
                ];
            });

            $finalizados = $todasPartidas
                ->filter(fn ($p) => in_array($p['estado'], ['finalizado', 'timeout'], true))
                ->sort(function ($a, $b) {
                    if ($a['tiempo_segundos'] !== $b['tiempo_segundos']) {
                        return $a['tiempo_segundos'] <=> $b['tiempo_segundos'];
                    }

                    return $a['id'] <=> $b['id'];
                })
                ->take(10)
                ->values()
                ->map(function ($item, $index) {
                    $item['rank'] = $index + 1;
                    return $item;
                });

            $primerFinalizado = $finalizados->firstWhere('estado', 'finalizado');
            $primerTerminado = $finalizados->first();
            $mejorTiempoFinalizado = $primerFinalizado['tiempo_segundos']
                ?? $primerTerminado['tiempo_segundos']
                ?? null;

            $finalizados = $finalizados->map(function ($item, $index) use ($mejorTiempoFinalizado) {
                if ($index === 0 || $item['estado'] !== 'finalizado' || $mejorTiempoFinalizado === null) {
                    $item['gap'] = null;
                    return $item;
                }

                $diff = $item['tiempo_segundos'] - $mejorTiempoFinalizado;
                $item['gap'] = $diff > 0 ? '+' . $this->formatSeconds($diff) . ' sobre el 1º' : null;

                return $item;
            })->values();

            $enProgreso = $todasPartidas
                ->filter(fn ($p) => $p['estado'] === 'jugando')
                ->sort(function ($a, $b) {
                    if ($a['peso'] !== $b['peso']) {
                        return $b['peso'] <=> $a['peso'];
                    }

                    if ($a['tiempo_segundos'] !== $b['tiempo_segundos']) {
                        return $a['tiempo_segundos'] <=> $b['tiempo_segundos'];
                    }

                    return $a['id'] <=> $b['id'];
                })
                ->take(10)
                ->values()
                ->map(function ($item, $index) {
                    $item['rank'] = $index + 1;
                    $item['gap'] = null;
                    return $item;
                });

            Log::info('Top10 datos resultado', [
                'finalizados' => $finalizados->count(),
                'en_progreso' => $enProgreso->count(),
            ]);

            return response()->json([
                'ticker' => $ticker,
                'top10' => $finalizados,
                'finalizados' => $finalizados,
                'en_progreso' => $enProgreso,
                'timestamp' => now()->timestamp // Useful for triggering animations on client if needed
            ]);
        } catch (\Throwable $e) {
            Log::error('Top10 error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error generando Top10'], 500);
        }
    }

}
