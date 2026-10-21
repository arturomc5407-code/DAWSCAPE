@extends('layouts.admin')

@push('admin-styles')
<style>
    .health-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .health-card {
        background: var(--surface-color);
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .health-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }

    .health-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .health-card-title {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .health-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .health-card .big-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    .health-card .sub-value {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    /* Status indicator (pulsing dot) */
    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        flex-shrink: 0;
    }

    .status-dot.online {
        background: var(--success-color);
        animation: pulse-green 2s infinite;
    }

    .status-dot.offline {
        background: var(--danger-color);
        animation: pulse-red 1.5s infinite;
    }

    @keyframes pulse-green {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
        50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    }

    @keyframes pulse-red {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
        50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    }

    .status-line {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
    }

    .status-line:last-child { border-bottom: none; }

    .status-label { font-weight: 600; color: var(--text-main); }
    .status-value { margin-left: auto; color: var(--text-muted); font-weight: 500; }
    .status-value.text-success { color: var(--success-color) !important; }
    .status-value.text-danger { color: var(--danger-color) !important; }

    /* Progress bar */
    .bar-track {
        width: 100%;
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        margin-top: 0.75rem;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    /* DB table list */
    .table-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .table-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.85rem;
    }

    .table-list li:last-child { border-bottom: none; }

    .table-list .tbl-name { color: var(--text-main); font-weight: 500; }
    .table-list .tbl-count {
        background: #f1f5f9;
        padding: 2px 10px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.8rem;
        color: var(--text-main);
    }

    /* Extensions grid */
    .ext-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 0.5rem;
    }

    .ext-badge {
        font-size: 0.7rem;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 600;
        background: #f1f5f9;
        color: var(--text-muted);
    }

    .ext-badge.critical {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title i { color: var(--brand-color); }

    .wide-card {
        grid-column: 1 / -1;
    }

    @media (max-width: 768px) {
        .health-grid {
            grid-template-columns: 1fr !important;
        }
        .health-card {
            padding: 1.2rem;
            border-radius: 12px;
        }
        .health-card .big-value {
            font-size: 1.4rem;
        }
        .health-card .sub-value {
            font-size: 0.8rem;
        }
        .status-line {
            padding: 0.6rem 0;
            font-size: 0.85rem;
        }
        .status-label {
            font-size: 0.85rem;
        }
        .status-value {
            font-size: 0.8rem;
            word-break: break-all;
        }
        .ext-grid {
            gap: 4px;
        }
        .ext-badge {
            font-size: 0.65rem;
            padding: 2px 8px;
        }
        .section-title {
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('admin-content')
<div class="page-header">
    <div>
        <h1 class="page-title">System Health</h1>
        <div class="page-subtitle">Monitor de rendimiento y estado del servidor en tiempo real</div>
    </div>
    <div style="font-size: 0.8rem; color: var(--text-muted);">
        <i class="ph ph-clock"></i> {{ now()->format('d/m/Y H:i:s') }}
    </div>
</div>

{{-- ROW 1: Status Services --}}
<div class="section-title"><i class="ph ph-activity"></i> Estado de Servicios</div>
<div class="health-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
    {{-- DB Status --}}
    <div class="health-card">
        <div class="health-card-header">
            <span class="health-card-title">Base de Datos</span>
            <div class="health-card-icon" style="background: {{ $dbOnline ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)' }}; color: {{ $dbOnline ? 'var(--success-color)' : 'var(--danger-color)' }};">
                <i class="ph ph-database"></i>
            </div>
        </div>
        <div style="display: flex; align-items: center;">
            <span class="status-dot {{ $dbOnline ? 'online' : 'offline' }}"></span>
            <span class="big-value" style="font-size: 1.3rem; color: {{ $dbOnline ? 'var(--success-color)' : 'var(--danger-color)' }};">
                {{ $dbOnline ? 'ONLINE' : 'OFFLINE' }}
            </span>
        </div>
        <div class="sub-value">Driver: MySQL</div>
    </div>

    {{-- PHP Status --}}
    <div class="health-card">
        <div class="health-card-header">
            <span class="health-card-title">PHP Runtime</span>
            <div class="health-card-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
                <i class="ph ph-code"></i>
            </div>
        </div>
        <div style="display: flex; align-items: center;">
            <span class="status-dot online"></span>
            <span class="big-value" style="font-size: 1.3rem; color: var(--success-color);">ONLINE</span>
        </div>
        <div class="sub-value">v{{ $phpVersion }}</div>
    </div>

    {{-- Laravel Status --}}
    <div class="health-card">
        <div class="health-card-header">
            <span class="health-card-title">Laravel Framework</span>
            <div class="health-card-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;">
                <i class="ph ph-terminal-window"></i>
            </div>
        </div>
        <div style="display: flex; align-items: center;">
            <span class="status-dot online"></span>
            <span class="big-value" style="font-size: 1.3rem; color: var(--success-color);">ONLINE</span>
        </div>
        <div class="sub-value">v{{ $laravelVersion }}</div>
    </div>

    {{-- Web Server --}}
    <div class="health-card">
        <div class="health-card-header">
            <span class="health-card-title">Servidor Web</span>
            <div class="health-card-icon" style="background: rgba(245,158,11,0.1); color: var(--warning-color);">
                <i class="ph ph-globe"></i>
            </div>
        </div>
        <div style="display: flex; align-items: center;">
            <span class="status-dot online"></span>
            <span class="big-value" style="font-size: 1.3rem; color: var(--success-color);">ONLINE</span>
        </div>
        <div class="sub-value">{{ $serverSoftware }}</div>
    </div>
</div>

{{-- ROW 2: Metrics --}}
<div class="section-title"><i class="ph ph-chart-line-up"></i> Métricas del Sistema</div>
<div class="health-grid">
    {{-- Memory Usage --}}
    <div class="health-card">
        <div class="health-card-header">
            <span class="health-card-title">Memoria PHP</span>
            <div class="health-card-icon" style="background: rgba(59,130,246,0.1); color: var(--brand-color);">
                <i class="ph ph-memory"></i>
            </div>
        </div>
        @php
            $memLimitVal = (int) $memoryLimit;
            $memLimitSuffix = strtoupper(substr(trim($memoryLimit), -1));
            if ($memLimitSuffix === 'G') {
                $memLimitBytes = $memLimitVal * 1024 * 1024 * 1024;
            } elseif ($memLimitSuffix === 'M') {
                $memLimitBytes = $memLimitVal * 1024 * 1024;
            } elseif ($memLimitSuffix === 'K') {
                $memLimitBytes = $memLimitVal * 1024;
            } else {
                $memLimitBytes = $memLimitVal;
            }
            $memPercent = $memLimitBytes > 0 ? round(($memoryUsage / $memLimitBytes) * 100, 1) : 0;
            $memColor = $memPercent < 70 ? 'var(--success-color)' : ($memPercent < 90 ? 'var(--warning-color)' : 'var(--danger-color)');
        @endphp
        <div class="big-value">{{ number_format($memoryUsage / 1024 / 1024, 1) }} MB</div>
        <div class="sub-value">Pico: {{ number_format($memoryPeak / 1024 / 1024, 1) }} MB &middot; Límite: {{ $memoryLimit }}</div>
        <div class="bar-track">
            <div class="bar-fill" style="width: {{ min($memPercent, 100) }}%; background: {{ $memColor }};"></div>
        </div>
    </div>

    {{-- Disk Usage --}}
    <div class="health-card">
        <div class="health-card-header">
            <span class="health-card-title">Espacio en Disco</span>
            <div class="health-card-icon" style="background: rgba(168,85,247,0.1); color: #a855f7;">
                <i class="ph ph-hard-drives"></i>
            </div>
        </div>
        @php
            $diskUsed = $diskTotal - $diskFree;
            $diskPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;
            $diskColor = $diskPercent < 70 ? 'var(--success-color)' : ($diskPercent < 90 ? 'var(--warning-color)' : 'var(--danger-color)');
        @endphp
        <div class="big-value">{{ number_format($diskFree / 1024 / 1024 / 1024, 1) }} GB</div>
        <div class="sub-value">Libre de {{ number_format($diskTotal / 1024 / 1024 / 1024, 1) }} GB ({{ $diskPercent }}% usado)</div>
        <div class="bar-track">
            <div class="bar-fill" style="width: {{ min($diskPercent, 100) }}%; background: {{ $diskColor }};"></div>
        </div>
    </div>

    {{-- Database Size --}}
    <div class="health-card">
        <div class="health-card-header">
            <span class="health-card-title">Tamaño de Base de Datos</span>
            <div class="health-card-icon" style="background: rgba(16,185,129,0.1); color: var(--success-color);">
                <i class="ph ph-database"></i>
            </div>
        </div>
        <div class="big-value">
            @if($dbSize >= 1048576)
                {{ number_format($dbSize / 1024 / 1024, 2) }} MB
            @else
                {{ number_format($dbSize / 1024, 1) }} KB
            @endif
        </div>
        <div class="sub-value">{{ number_format($totalRecords) }} registros totales</div>
    </div>
</div>

{{-- ROW 3: Config + Tables --}}
<div class="section-title"><i class="ph ph-gear-six"></i> Configuración y Datos</div>
<div class="health-grid" style="grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));">
    {{-- Config Panel --}}
    <div class="health-card">
        <div class="health-card-header">
            <span class="health-card-title">Configuración del Entorno</span>
        </div>
        <div class="status-line">
            <span class="status-label">PHP</span>
            <span class="status-value">v{{ $phpVersion }}</span>
        </div>
        <div class="status-line">
            <span class="status-label">Laravel</span>
            <span class="status-value">v{{ $laravelVersion }}</span>
        </div>
        <div class="status-line">
            <span class="status-label">Servidor</span>
            <span class="status-value">{{ $serverSoftware }}</span>
        </div>
        <div class="status-line">
            <span class="status-label">Caché</span>
            <span class="status-value">{{ ucfirst($cacheDriver) }}</span>
        </div>
        <div class="status-line">
            <span class="status-label">Sesión</span>
            <span class="status-value">{{ ucfirst($sessionDriver) }}</span>
        </div>
        <div class="status-line">
            <span class="status-label">Cola</span>
            <span class="status-value">{{ ucfirst($queueDriver) }}</span>
        </div>
        <div class="status-line">
            <span class="status-label">Entorno</span>
            <span class="status-value">{{ ucfirst(app()->environment()) }}</span>
        </div>
        <div class="status-line">
            <span class="status-label">Debug</span>
            <span class="status-value {{ config('app.debug') ? 'text-danger' : 'text-success' }}">
                {{ config('app.debug') ? 'Activado' : 'Desactivado' }}
            </span>
        </div>
    </div>

    {{-- Table records --}}
    <div class="health-card">
        <div class="health-card-header">
            <span class="health-card-title">Registros por Tabla</span>
        </div>
        <ul class="table-list">
            @foreach($tableCounts as $table => $count)
                <li>
                    <a href="{{ route('admin.database.show', $tableMap[$table]) }}" class="tbl-name" style="text-decoration: none; color: inherit; display: flex; align-items: center; transition: color 0.2s;" onmouseover="this.style.color='var(--brand-accent)'" onmouseout="this.style.color='inherit'">
                        <i class="ph ph-table" style="margin-right: 6px; color: var(--text-muted);"></i>{{ $table }}
                    </a>
                    <span class="tbl-count">{{ number_format($count) }}</span>
                </li>
            @endforeach
        </ul>
        <div style="margin-top: 0.75rem; text-align: right; font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
            Total: {{ number_format($totalRecords) }} registros
        </div>
    </div>
</div>

{{-- ROW 4: PHP Extensions --}}
<div class="section-title"><i class="ph ph-puzzle-piece"></i> Extensiones PHP ({{ count($extensions) }})</div>
<div class="health-grid">
    <div class="health-card wide-card">
        <div class="health-card-header">
            <span class="health-card-title">Extensiones Críticas</span>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 1rem;">
            @foreach($criticalExtensions as $ext)
                @php $loaded = in_array($ext, $extensions); @endphp
                <div style="display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600;
                    background: {{ $loaded ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)' }};
                    color: {{ $loaded ? '#059669' : '#dc2626' }};
                    border: 1px solid {{ $loaded ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }};">
                    <span class="status-dot {{ $loaded ? 'online' : 'offline' }}" style="width: 6px; height: 6px; margin: 0;"></span>
                    {{ $ext }}
                </div>
            @endforeach
        </div>

        <div class="health-card-header" style="margin-top: 1rem;">
            <span class="health-card-title">Todas las Extensiones Cargadas</span>
        </div>
        <div class="ext-grid">
            @foreach($extensions as $ext)
                <span class="ext-badge {{ in_array($ext, $criticalExtensions) ? 'critical' : '' }}">{{ $ext }}</span>
            @endforeach
        </div>
    </div>
</div>
@endsection
