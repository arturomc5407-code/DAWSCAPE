<?php

namespace App\Services;

use App\Models\Partida;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminStatsService
{
    /**
     * Obtiene el listado de partidas ordenado.
     * @return Collection
     */
    public function getPartidasOrdered()
    {
        $FINALIZADO = config('fases.fases.finalizado.db');
        $TIMEOUT = config('fases.fases.timeout.db');

        return Partida::orderByRaw("
            CASE
                WHEN fase_actual = '{$FINALIZADO}' THEN 0
                WHEN fase_actual = '{$TIMEOUT}' THEN 1
                ELSE 2
            END")
            ->orderBy('tiempo_segundos', 'asc')
            ->get();
    }

    /**
     * Obtiene el Top 5 de equipos finalizados más rápidos.
     * Incluye penalización en el cálculo del tiempo total.
     * @return Collection
     */
    public function getTop5()
    {
        return Partida::where('fase_actual', config('fases.fases.finalizado.db'))
            ->orderByRaw('(tiempo_segundos + COALESCE(penalizacion_segundos, 0)) ASC')
            ->limit(5)
            ->get();
    }

    /**
     * Calcula los KPIs optimizados con consultas Agregadas directamente en BD.
     * @return array
     */
    public function getMetrics()
    {
        $FINALIZADO = config('fases.fases.finalizado.db');
        $TIMEOUT = config('fases.fases.timeout.db');

        // En lugar de traer todos los registros a memoria para contarlos (O(n)),
        // delegamos el conteo al motor de base de datos (O(1) o indexado).
        $metrics = Partida::select(
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN fase_actual = '{$FINALIZADO}' THEN 1 ELSE 0 END) as finalizados"),
            DB::raw("SUM(CASE WHEN fase_actual = '{$TIMEOUT}' THEN 1 ELSE 0 END) as timeout"),
            DB::raw("AVG(CASE WHEN fase_actual = '{$FINALIZADO}' THEN tiempo_segundos ELSE NULL END) as tiempo_medio")
        )->first();

        $totalEquipos = (int) $metrics->total;
        $equiposFinalizados = (int) $metrics->finalizados;
        $equiposTimeout = (int) $metrics->timeout;
        $equiposActivos = $totalEquipos - $equiposFinalizados - $equiposTimeout;
        $tiempoMedioSegundos = (float) $metrics->tiempo_medio;

        return [
            'total' => $totalEquipos,
            'finalizados' => $equiposFinalizados,
            'timeout' => $equiposTimeout,
            'activos' => $equiposActivos,
            'promedio' => $tiempoMedioSegundos > 0 ? floor($tiempoMedioSegundos / 60) . 'm' : '--',
            'promedio_sec' => $tiempoMedioSegundos
        ];
    }

    /**
     * Obtiene los datos del gráfico de la tarta (Chart.js)
     * Optimizando con un GROUP BY puro en SQL.
     * @return array
     */
    public function getChartData()
    {
        $fases = Partida::select('fase_actual', DB::raw('count(*) as total'))
            ->groupBy('fase_actual')
            ->pluck('total', 'fase_actual');

        return [
            'labels' => $fases->keys()->toArray(),
            'data' => $fases->values()->toArray()
        ];
    }

    /**
     * Calcula la media de segundos invertidos en cada fase para detectar los Cuellos de Botella (Prueba más Letal).
     * @return array
     */
    public function getBottlenecks()
    {
        $hitos = \App\Models\HitoFase::orderBy('partida_id')->orderBy('segundos_transcurridos', 'asc')->get();

        $tiemposPorFase = [];
        $lastSegundos = [];

        foreach ($hitos as $hito) {
            $pid = $hito->partida_id;

            // Extraer el nombre de la prueba real removiendo sufijos (Ej: "Prueba 1 Mario Superada" -> "Prueba 1 Mario")
            $faseName = str_replace(' Superada', '', $hito->fase);
            $currentSecs = (int) $hito->segundos_transcurridos;

            $prevSecs = $lastSegundos[$pid] ?? 0;
            $delta = $currentSecs - $prevSecs;

            if (!isset($tiemposPorFase[$faseName])) {
                $tiemposPorFase[$faseName] = ['total' => 0, 'count' => 0];
            }

            $tiemposPorFase[$faseName]['total'] += $delta;
            $tiemposPorFase[$faseName]['count']++;

            $lastSegundos[$pid] = $currentSecs;
        }

        $resultados = [];
        foreach ($tiemposPorFase as $fase => $data) {
            $resultados[] = [
                'fase' => $fase,
                'media_segundos' => (int) round($data['total'] / $data['count']),
                'media_minutos' => round(($data['total'] / $data['count']) / 60, 1)
            ];
        }

        usort($resultados, function($a, $b) {
            return $b['media_segundos'] <=> $a['media_segundos'];
        });

        return $resultados;
    }
}
