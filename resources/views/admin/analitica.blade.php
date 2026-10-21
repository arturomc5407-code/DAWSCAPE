@extends('layouts.admin')

@push('admin-styles')
<style>
    .chart-container {
        background: var(--surface-color);
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        margin-bottom: 2rem;
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.3rem;
    }

    .chart-subtitle {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--surface-color);
        border-radius: 12px;
        padding: 1.25rem;
        border: 1px solid var(--border-color);
        text-align: center;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ef4444, #f97316);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .stat-card .stat-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .table-analytics {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-analytics thead th {
        background: #f1f5f9;
        padding: 0.75rem 1rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        border-bottom: 2px solid var(--border-color);
        text-align: left;
    }

    .table-analytics tbody td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
    }

    .table-analytics tbody tr:hover {
        background: #f8fafc;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
    }

    /* ========== ANALÍTICA — MOBILE RESPONSIVE ========== */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            padding: 1rem;
        }
        .stat-card .stat-value {
            font-size: 1.4rem;
        }
        .stat-card .stat-label {
            font-size: 0.7rem;
        }

        .chart-container {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .chart-title { font-size: 1rem; }
        .chart-subtitle { font-size: 0.8rem; margin-bottom: 1rem; }

        /* Tablas analítica → tarjetas */
        .table-analytics thead { display: none; }
        .table-analytics,
        .table-analytics tbody { display: block; width: 100%; }
        .table-analytics tr {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .table-analytics td {
            padding: 0;
            border-bottom: none;
            font-size: 0.85rem;
        }
        .table-analytics td:first-child {
            flex: 1;
            min-width: 0;
        }
        .table-analytics td:first-child strong {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }

    @media (max-width: 400px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('admin-content')
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="ph ph-chart-bar" style="color: var(--brand-accent);"></i> Analítica de Pruebas</h1>
            <p class="page-subtitle">Análisis de dificultad por prueba. Identifica los cuellos de botella del escape room.</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    @php
        $totalFallos = $fallosPorPrueba->sum('total_fallos');
        $totalPistas = $pistasPorPrueba->sum('total_pistas');
        $pruebaMasLetal = count($bottlenecks) > 0 ? $bottlenecks[0]['fase'] : '--';
        $tiempoMax = count($bottlenecks) > 0 ? $bottlenecks[0]['media_minutos'] . ' min' : '--';
    @endphp
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $totalFallos }}</div>
            <div class="stat-label">Total Fallos Registrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalPistas }}</div>
            <div class="stat-label">Pistas Utilizadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size: 1.2rem;">{{ $pruebaMasLetal }}</div>
            <div class="stat-label">Prueba más Letal</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $tiempoMax }}</div>
            <div class="stat-label">Tiempo Medio Máximo</div>
        </div>
    </div>

    {{-- Chart: Tiempo medio por prueba --}}
    <div class="chart-container">
        <div class="chart-title">Tiempo Medio por Prueba</div>
        <div class="chart-subtitle">Minutos medios que los equipos invierten en cada prueba (cuello de botella)</div>
        <canvas id="chartTiempos" height="100"></canvas>
    </div>

    {{-- Chart: Pistas usadas por prueba --}}
    <div class="chart-container">
        <div class="chart-title">Pistas Utilizadas por Prueba</div>
        <div class="chart-subtitle">Número total de pistas solicitadas en cada prueba</div>
        <canvas id="chartPistas" height="80"></canvas>
    </div>

    {{-- Tabla detalle de fallos --}}
    @if($fallosPorPrueba->count() > 0)
    <div class="chart-container">
        <div class="chart-title">Detalle de Fallos por Prueba</div>
        <div class="chart-subtitle">Intentos fallidos registrados en el log del juego</div>
        <table class="table-analytics">
            <thead>
                <tr>
                    <th>Fase / Prueba</th>
                    <th style="text-align:center;">Fallos Totales</th>
                    <th style="text-align:center;">Nivel</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fallosPorPrueba->sortByDesc('total_fallos') as $fallo)
                <tr>
                    <td><strong>{{ $fallo->fase }}</strong></td>
                    <td style="text-align:center;">
                        <span class="badge-danger">{{ $fallo->total_fallos }}</span>
                    </td>
                    <td style="text-align:center;">
                        @if($fallo->total_fallos >= 20)
                            <span class="badge-danger">Crítico</span>
                        @elseif($fallo->total_fallos >= 10)
                            <span class="badge-warning">Alto</span>
                        @else
                            <span style="color: var(--success-color); font-weight: 600;">Normal</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Tabla resumen de bottlenecks --}}
    @if(count($bottlenecks) > 0)
    <div class="chart-container">
        <div class="chart-title">Ranking de Dificultad (Cuellos de Botella)</div>
        <div class="chart-subtitle">Pruebas ordenadas por tiempo medio invertido (de mayor a menor)</div>
        <table class="table-analytics">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Prueba</th>
                    <th style="text-align:center;">Tiempo Medio</th>
                    <th style="text-align:center;">Segundos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bottlenecks as $i => $bn)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $bn['fase'] }}</strong></td>
                    <td style="text-align:center;">
                        <span class="{{ $i < 3 ? 'badge-danger' : 'badge-warning' }}">{{ $bn['media_minutos'] }} min</span>
                    </td>
                    <td style="text-align:center;">{{ $bn['media_segundos'] }}s</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===== Chart 1: Tiempo medio por prueba =====
    const bottlenecks = @json($bottlenecks);
    const labelsTime = bottlenecks.map(b => b.fase.replace(/Prueba \d+ /, 'P'));
    const dataTime = bottlenecks.map(b => b.media_minutos);

    // Ordenar por número de prueba para el gráfico (no por dificultad)
    const sorted = bottlenecks
        .map((b, i) => ({ ...b, originalLabel: labelsTime[i] }))
        .sort((a, b) => {
            const numA = parseInt(a.fase.match(/\d+/)?.[0] || 0);
            const numB = parseInt(b.fase.match(/\d+/)?.[0] || 0);
            return numA - numB;
        });

    const colorsTime = sorted.map(b => {
        if (b.media_minutos >= 5) return 'rgba(239, 68, 68, 0.85)';      // rojo
        if (b.media_minutos >= 3) return 'rgba(249, 115, 22, 0.85)';     // naranja
        return 'rgba(245, 158, 11, 0.75)';                                // amarillo
    });

    const bordersTime = sorted.map(b => {
        if (b.media_minutos >= 5) return '#dc2626';
        if (b.media_minutos >= 3) return '#ea580c';
        return '#d97706';
    });

    if (document.getElementById('chartTiempos')) {
        new Chart(document.getElementById('chartTiempos'), {
            type: 'bar',
            data: {
                labels: sorted.map(b => b.fase),
                datasets: [{
                    label: 'Tiempo medio (min)',
                    data: sorted.map(b => b.media_minutos),
                    backgroundColor: colorsTime,
                    borderColor: bordersTime,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        padding: 12,
                        callbacks: {
                            label: ctx => ctx.parsed.y + ' minutos de media'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Minutos', color: '#64748b', font: { weight: '600' } },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { color: '#64748b' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#334155', font: { weight: '600', size: 11 }, maxRotation: 45 }
                    }
                }
            }
        });
    }

    // ===== Chart 2: Pistas por prueba =====
    const pistas = @json($pistasPorPrueba);
    const labelsPistas = pistas.map(p => 'Prueba ' + p.prueba_numero);
    const dataPistas = pistas.map(p => p.total_pistas);

    const colorsPistas = dataPistas.map(v => {
        if (v >= 10) return 'rgba(239, 68, 68, 0.8)';
        if (v >= 5) return 'rgba(249, 115, 22, 0.8)';
        return 'rgba(245, 158, 11, 0.7)';
    });

    if (document.getElementById('chartPistas')) {
        new Chart(document.getElementById('chartPistas'), {
            type: 'bar',
            data: {
                labels: labelsPistas,
                datasets: [{
                    label: 'Pistas usadas',
                    data: dataPistas,
                    backgroundColor: colorsPistas,
                    borderColor: colorsPistas.map(c => c.replace(/[\d.]+\)$/, '1)')),
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Pistas', color: '#64748b', font: { weight: '600' } },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { color: '#64748b', stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#334155', font: { weight: '600' } }
                    }
                }
            }
        });
    }
});
</script>
@endpush
