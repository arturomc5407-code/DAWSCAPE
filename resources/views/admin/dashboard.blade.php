@extends('layouts.admin')

@section('title', 'Admin Dashboard Premium | DAWSCAPE')

@push('admin-styles')
    <style>
        /* --- CARDS (KPIs) --- */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .kpi-card {
            background: var(--surface-color);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            gap: 1.2rem;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }
        .kpi-card.kpi-blue::before { background: #3b82f6; }
        .kpi-card.kpi-green::before { background: #10b981; }
        .kpi-card.kpi-yellow::before { background: #f59e0b; }
        .kpi-card.kpi-purple::before { background: #8b5cf6; }

        .kpi-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .kpi-blue .kpi-icon-wrap { background: #eff6ff; color: #3b82f6; }
        .kpi-green .kpi-icon-wrap { background: #ecfdf5; color: #10b981; }
        .kpi-yellow .kpi-icon-wrap { background: #fffbeb; color: #f59e0b; }
        .kpi-purple .kpi-icon-wrap { background: #f5f3ff; color: #8b5cf6; }

        .kpi-info h4 {
            margin: 0;
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kpi-info p {
            margin: 5px 0 0 0;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1;
        }

        /* --- DOS COLUMNAS (Chart + Podium) --- */
        .mid-grid {
            display: grid;
            grid-template-columns: 2fr 3fr;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .panel-heading {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.2px;
        }

        .panel-heading i { color: var(--brand-color); }

        .chart-container { position: relative; height: 250px; width: 100%; display: flex; justify-content: center; }

        /* Podium List */
        .podium-list { display: flex; flex-direction: column; gap: 12px; }
        .podium-item {
            display: flex; align-items: center; padding: 12px 16px; border-radius: 10px;
            background: #ffffff; border: 1px solid var(--border-color); transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .podium-item:hover { border-color: #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); }

        .pos-badge {
            width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.1rem; margin-right: 15px;
        }
        .pos-1 { background: #fef08a; color: #854d0e; }
        .pos-2 { background: #e2e8f0; color: #475569; }
        .pos-3 { background: #fed7aa; color: #9a3412; }
        .pos-other { background: #f1f5f9; color: #64748b; }

        .podium-details { flex: 1; min-width: 0; }
        .podium-name { font-weight: 600; color: var(--text-main); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .podium-class { font-size: 0.8rem; color: var(--text-muted); }
        .podium-time { font-weight: 700; color: var(--success-color); font-size: 1.1rem; font-variant-numeric: tabular-nums; }

        /* --- TABLE --- */
        .table-responsive { border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); background: var(--surface-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .table-custom { width: 100%; margin: 0; border-collapse: collapse; }
        .table-custom th {
            background-color: #f8fafc; color: var(--text-muted); font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.5px; font-size: 0.75rem; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color);
        }
        .table-custom td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid var(--border-color); color: var(--text-main); }
        .table-custom tr:hover td { background-color: #f8fafc; }
        .table-custom tr:last-child td { border-bottom: none; }

        /* Avatares */
        .team-cell { display: flex; align-items: center; gap: 15px; }
        .avatar {
            width: 40px; height: 40px; border-radius: 50%; border: 2px solid #ffffff;
            color: var(--brand-color); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem;
            background: #eff6ff; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .team-meta .t-name { font-weight: 600; margin-bottom: 2px; }
        .team-meta .t-email { font-size: 0.8rem; color: var(--text-muted); }

        /* Badges */
        .status-pill {
            display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px;
            font-size: 0.75rem; font-weight: 600;
        }

        .time-display { font-weight: 600; font-size: 0.95rem; color: var(--text-main); font-variant-numeric: tabular-nums; }
        .text-live { color: #2563eb; }
        .text-success { color: #15803d; }
        .text-danger { color: #b91c1c; }

        /* Botones Acción */
        .btn-action {
            width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid transparent; cursor: pointer; transition: all 0.2s; color: var(--text-muted); background: transparent;
        }
        .btn-action:hover { color: var(--danger-color); background: #fee2e2; }

        .export-btn {
            background: #ffffff; color: var(--text-main); border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: 8px;
            font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; font-size: 0.9rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .export-btn:hover { border-color: #cbd5e1; background: #f8fafc; color: var(--text-main); }
        .export-btn i { color: var(--brand-color); }

        /* Buscador */
        .search-wrapper { position: relative; margin-bottom: 1.5rem; max-width: 400px; }
        .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; }
        .search-input { width: 100%; padding: 0.6rem 1rem 0.6rem 2.5rem; border: 1px solid var(--border-color); border-radius: 8px; background: #ffffff; color: var(--text-main); transition: all 0.2s; outline: none; font-size: 0.95rem; }
        .search-input:focus { border-color: var(--brand-color); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }

        /* ========== DASHBOARD MOBILE ========== */
        @media (max-width: 768px) {
            /* KPIs */
            .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
            .kpi-card { padding: 1rem; gap: 0.8rem; }
            .kpi-icon-wrap { width: 38px; height: 38px; font-size: 1.2rem; }
            .kpi-info h4 { font-size: 0.7rem; }
            .kpi-info p { font-size: 1.2rem; }

            /* Chart + Podium */
            .mid-grid { grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1.5rem; }
            .chart-container { height: 200px; }
            .podium-item { padding: 10px 12px; }
            .podium-name { font-size: 0.85rem; }
            .podium-time { font-size: 0.95rem; }

            /* Buscador full-width */
            .search-wrapper { max-width: 100%; }

            /* Export buttons */
            .export-btn { padding: 0.4rem 0.7rem; font-size: 0.8rem; }

            /* ====== TABLA EQUIPOS → TARJETAS ====== */
            #mainTable { border-collapse: separate; border-spacing: 0; }

            .table-responsive {
                border: none;
                box-shadow: none;
                border-radius: 0;
                background: transparent;
                overflow: visible;
            }

            #mainTable thead { display: none; }

            #mainTable,
            #mainTable tbody {
                display: block;
                width: 100%;
            }

            #mainTable tr.team-row {
                display: -ms-grid;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0;
                background: #ffffff;
                border: 1px solid var(--border-color);
                border-radius: 12px;
                padding: 1rem;
                margin-bottom: 0.75rem;
                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                position: relative;
            }

            #mainTable tr.team-row:hover td { background: transparent; }

            #mainTable td {
                display: block;
                padding: 0.3rem 0;
                border-bottom: none;
                text-align: left !important;
                white-space: normal;
                font-size: 0.9rem;
            }

            /* Celda 1: ID — esquina superior izq como badge sutil */
            #mainTable td:nth-child(1) {
                grid-column: 1 / -1;
                font-size: 0.7rem;
                color: var(--text-muted) !important;
                padding-bottom: 0.5rem;
                margin-bottom: 0.25rem;
                border-bottom: 1px solid #f1f5f9;
            }

            /* Celda 2: Equipo & Contacto — full-width */
            #mainTable td:nth-child(2) {
                grid-column: 1 / -1;
                padding-bottom: 0.6rem;
            }
            #mainTable .team-cell { gap: 10px; }
            #mainTable .avatar { width: 36px; height: 36px; font-size: 1.1rem; flex-shrink: 0; }
            #mainTable .team-meta .t-name { font-size: 0.95rem; }
            #mainTable .team-meta .t-email { font-size: 0.75rem; word-break: break-all; }

            /* Celda 3: Clase */
            #mainTable td:nth-child(3) {
                grid-column: 1;
            }
            #mainTable td:nth-child(3)::before {
                content: 'Clase';
                display: block;
                font-size: 0.65rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--text-muted);
                margin-bottom: 3px;
            }

            /* Celda 4: Estado */
            #mainTable td:nth-child(4) {
                grid-column: 2;
            }
            #mainTable td:nth-child(4)::before {
                content: 'Estado';
                display: block;
                font-size: 0.65rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--text-muted);
                margin-bottom: 3px;
            }

            /* Celda 5: Códice */
            #mainTable td:nth-child(5) {
                grid-column: 1;
                text-align: left !important;
                padding-top: 0.4rem;
            }
            #mainTable td:nth-child(5)::before {
                content: 'Códice (P7)';
                display: block;
                font-size: 0.65rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--text-muted);
                margin-bottom: 3px;
            }

            /* Celda 6: Tiempo */
            #mainTable td:nth-child(6) {
                grid-column: 2;
                padding-top: 0.4rem;
            }
            #mainTable td:nth-child(6)::before {
                content: 'Tiempo';
                display: block;
                font-size: 0.65rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--text-muted);
                margin-bottom: 3px;
            }

            /* Celda 7: Pistas */
            #mainTable td:nth-child(7) {
                grid-column: 1;
                text-align: left !important;
                padding-top: 0.4rem;
            }
            #mainTable td:nth-child(7)::before {
                content: 'Pistas';
                display: block;
                font-size: 0.65rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--text-muted);
                margin-bottom: 3px;
            }

            /* Celda 8: Borrar — botón grande */
            #mainTable td:nth-child(8) {
                grid-column: 2;
                display: flex;
                align-items: flex-end;
                justify-content: flex-end;
                padding-top: 0.4rem;
            }

            #mainTable .btn-action {
                width: 44px;
                height: 44px;
                border-radius: 10px;
                border: 1px solid #fecaca;
                background: #fee2e2;
                color: #dc2626;
                font-size: 1.2rem;
            }

            /* Fila vacía (empty state) */
            #mainTable tr:not(.team-row) {
                display: block;
            }
            #mainTable tr:not(.team-row) td {
                display: block;
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 400px) {
            .kpi-grid { grid-template-columns: 1fr; }
            #mainTable tr.team-row {
                grid-template-columns: 1fr;
            }
            #mainTable td:nth-child(3),
            #mainTable td:nth-child(4),
            #mainTable td:nth-child(5),
            #mainTable td:nth-child(6),
            #mainTable td:nth-child(7),
            #mainTable td:nth-child(8) {
                grid-column: 1;
            }
        }

        /* ========== INSCRIPCIONES PENDIENTES — MOBILE ========== */
        @media (max-width: 768px) {
            #inscripciones-panel .podium-item {
                flex-wrap: wrap;
                gap: 8px;
            }
            #inscripciones-panel .podium-item > div[style*="white-space: nowrap"] {
                flex-basis: 100%;
                order: 3;
                font-size: 0.7rem;
                margin-right: 0;
            }
        }
    </style>
@endpush

@section('admin-content')
        <div class="page-header">
            <div>
                <h1 class="page-title">Visión General</h1>
                <div class="page-subtitle">Monitorización en tiempo real de la actividad del Escape Room.</div>
            </div>
            <div style="display:flex; gap: 10px; align-items:center; flex-wrap: wrap;">
                <button onclick="window.location.reload()" class="export-btn">
                    <i class="ph ph-arrows-clockwise"></i> Recargar
                </button>

                <a href="{{ route('top10') }}" target="_blank" class="export-btn">
                    <i class="ph ph-monitor-play"></i> Ver Top 10
                </a>

                <a href="{{ route('admin.prueba.final') }}" target="_blank" class="export-btn">
                    <i class="ph ph-certificate"></i> Ver Diploma
                </a>

                <a href="/admin/exportar-top20" target="_blank" class="export-btn">
                    <i class="ph ph-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center">
                <i class="ph ph-check-circle me-2" style="font-size:1.5rem;"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center">
                <i class="ph ph-warning me-2" style="font-size:1.5rem;"></i> {{ session('error') }}
            </div>
        @endif

        <!-- TARJETAS DE MÉTRICAS (KPIs) -->
        <div class="kpi-grid">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon-wrap">
                    <i class="ph ph-users-three"></i>
                </div>
                <div class="kpi-info">
                    <h4>Total Inscritos</h4>
                    <p>{{ $totalInscritos }}</p>
                </div>
            </div>
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon-wrap">
                    <i class="ph ph-clipboard"></i>
                </div>
                <div class="kpi-info">
                    <h4>Pendientes de Inicio</h4>
                    <p>{{ $inscritosNoIniciados }}</p>
                </div>
            </div>
            <div class="kpi-card kpi-yellow">
                <div class="kpi-icon-wrap">
                    <i class="ph ph-activity"></i>
                </div>
                <div class="kpi-info">
                    <h4>En Progreso</h4>
                    <p>{{ $equiposActivos }}</p>
                </div>
            </div>
            <div class="kpi-card kpi-green">
                <div class="kpi-icon-wrap">
                    <i class="ph ph-flag-checkered"></i>
                </div>
                <div class="kpi-info">
                    <h4>Completados</h4>
                    <p>{{ $equiposFinalizados }}</p>
                </div>
            </div>
            <div class="kpi-card kpi-purple">
                <div class="kpi-icon-wrap">
                    <i class="ph ph-timer"></i>
                </div>
                <div class="kpi-info">
                    <h4>Promedio Éxito</h4>
                    <p>
                        @if($tiempoMedioSegundos > 0)
                            {{ floor($tiempoMedioSegundos / 60) }}<span style="font-size:1rem; color:var(--text-muted); font-weight:600; margin-left:2px;">m</span>
                        @else
                            --
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- NUEVA SECCIÓN: INSCRIPCIONES PENDIENTES -->
        @if($inscritosNoIniciados > 0)
        <div class="glass-panel" id="inscripciones-panel" style="margin-bottom: 2.5rem; border-left: 4px solid #3b82f6;">
            <div class="panel-heading"><i class="ph ph-clipboard-text"></i> Equipos Inscritos (Sin Iniciar)</div>
            <div style="max-height: 400px; overflow-y: auto; border-radius: 0 0 12px 12px;">
                <div class="podium-list" style="margin: 0;">
                    @foreach($inscripcionesPendientes as $insc)
                    <div class="podium-item" style="background: linear-gradient(135deg, #f0f9ff, #ffffff); border-left: 3px solid #3b82f6; display: flex; align-items: center; padding: 12px 16px; border-bottom: 1px solid #e0e7ff; margin: 0;">
                        <div class="pos-badge" style="background: #dbeafe; color: #3b82f6; box-shadow: none;">
                            {{ strtoupper(substr($insc->nombre_equipo, 0, 1)) }}
                        </div>
                        <div style="flex: 1; min-width: 0; margin: 0 12px;">
                            <div style="font-weight: 600; color: var(--text-main); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $insc->nombre_equipo }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $insc->correo }} | {{ $insc->clase }}
                            </div>
                        </div>
                        <div style="font-size: 0.75rem; color: #64748b; white-space: nowrap; margin-right: 8px;">
                            {{ $insc->created_at->diffForHumans() }}
                        </div>
                        <form action="/admin/inscripcion/{{ $insc->id }}" method="POST" onsubmit="return confirm('¿Eliminar inscripción de {{ $insc->nombre_equipo }}?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action" title="Eliminar inscripción" style="background: #fee2e2; color: #dc2626;">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- GRÁFICO Y PODIUM -->
        <div class="mid-grid">
            <!-- Gráfico Donut de Fases -->
            <div class="glass-panel">
                <div class="panel-heading"><i class="ph ph-chart-pie-slice"></i> Distribución por Fases</div>
                <div class="chart-container">
                    @if($totalEquipos > 0)
                        <canvas id="fasesChart"></canvas>
                    @else
                        <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#94a3b8; font-style:italic;">
                            No hay datos suficientes
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top 5 Podium Rápido -->
            <div class="glass-panel">
                <div class="panel-heading"><i class="ph ph-trophy"></i> Salón de la Fama (Top 5)</div>
                @if($top5->isNotEmpty())
                    <div class="podium-list">
                        @foreach($top5 as $index => $ranking)
                            <div class="podium-item">
                                <div class="pos-badge {{ $index == 0 ? 'pos-1' : ($index == 1 ? 'pos-2' : ($index == 2 ? 'pos-3' : 'pos-other')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="podium-details">
                                    <div class="podium-name">{{ $ranking->nombre_equipo }}</div>
                                    <div class="podium-class">{{ $ranking->clase }}</div>
                                </div>
                                <div class="podium-time">
                                    {{ floor((int)($ranking->tiempo_segundos ?? 0) / 60) }}m {{ str_pad((int)($ranking->tiempo_segundos ?? 0) % 60, 2, '0', STR_PAD_LEFT) }}s
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center; padding: 2rem; color:#94a3b8;">
                        <i class="ph ph-medal" style="font-size:3rem; opacity:0.5; margin-bottom:1rem;"></i>
                        <p>Aún no hay equipos que hayan escapado del sistema.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- TABLA PRINCIPAL DE EQUIPOS -->
        <h2 style="font-size:1.3rem; font-weight:700; margin-bottom:1.2rem; color:var(--text-main);">Listado Completo de Equipos</h2>

        <div class="search-wrapper">
            <i class="ph ph-magnifying-glass"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar por nombre, correo o clase..." onkeyup="filterTable()">
        </div>

        <div class="table-responsive">
            <table class="table-custom" id="mainTable">
                <thead>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th style="width: 30%">Equipo & Contacto</th>
                        <th style="width: 10%">Clase</th>
                        <th style="width: 15%">Estado / Progreso</th>
                        <th style="width: 10%; text-align:center;">Códice (P7)</th>
                        <th style="width: 10%">Tiempo</th>
                        <th style="width: 10%">Pistas</th>
                        <th style="width: 10%; text-align:center;">Borrar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partidas as $index => $partida)
                    <tr class="team-row">
                        <td style="color:var(--text-muted); font-weight:600;">#{{ str_pad($partida->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="team-cell">
                                @php
                                    $bgAvatar = $partida->color_hex ? $partida->color_hex : 'linear-gradient(135deg, #60a5fa, #3b82f6)';
                                @endphp
                                <div class="avatar" style="background: {{ $bgAvatar }}; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; color: white;">
                                    @if($partida->icono)
                                        <i class="ph-fill {{ $partida->icono }}"></i>
                                    @else
                                        <span style="font-weight: bold; font-size: 1.2rem;">{{ strtoupper(substr($partida->nombre_equipo, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="team-meta">
                                    <div class="t-name">{{ $partida->nombre_equipo }}</div>
                                    <div class="t-email">{{ $partida->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span style="background:#f1f5f9; padding:4px 10px; border-radius:6px; font-size:0.85rem; font-weight:600; color:var(--text-muted);">{{ $partida->clase }}</span></td>
                        <td>
                            @php
                                $badges = [
                                    config('fases.fases.prueba1.db') => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'icon' => 'ph-spinner-gap'],
                                    config('fases.fases.prueba2.db') => ['bg' => '#f3e8ff', 'color' => '#6d28d9', 'icon' => 'ph-spinner-gap'],
                                    config('fases.fases.prueba3.db') => ['bg' => '#ffedd5', 'color' => '#c2410c', 'icon' => 'ph-spinner-gap'],
                                    config('fases.fases.prueba4.db') => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'icon' => 'ph-spinner-gap'],
                                    config('fases.fases.prueba5.db') => ['bg' => '#cffafe', 'color' => '#0e7490', 'icon' => 'ph-spinner-gap'],
                                    config('fases.fases.prueba6.db') => ['bg' => '#fef08a', 'color' => '#a16207', 'icon' => 'ph-spinner-gap'],
                                    config('fases.fases.prueba7.db') => ['bg' => '#dcfce7', 'color' => '#15803d', 'icon' => 'ph-spinner-gap'],
                                    config('fases.fases.prueba8.db') => ['bg' => '#fce7f3', 'color' => '#be185d', 'icon' => 'ph-spinner-gap'],
                                    config('fases.fases.prueba9.db') => ['bg' => '#e0f2fe', 'color' => '#0369a1', 'icon' => 'ph-spinner-gap'],
                                    config('fases.fases.finalizado.db') => ['bg' => '#d1fae5', 'color' => '#047857', 'icon' => 'ph-check-circle'],
                                    config('fases.fases.timeout.db') => ['bg' => '#f3f4f6', 'color' => '#374151', 'icon' => 'ph-clock']
                                ];
                                $badge = $badges[$partida->fase_actual] ?? ['bg' => '#f1f5f9', 'color' => '#475569', 'icon' => 'ph-spinner-gap'];
                                $stageText = match($partida->fase_actual) {
                                    config('fases.fases.finalizado.db') => 'COMPLETADO',
                                    config('fases.fases.timeout.db') => 'TIMEOUT',
                                    default => strtoupper($partida->fase_actual)
                                };
                            @endphp
                            <span class="status-pill" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-weight: 600; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px; box-shadow: none;">
                                <i class="ph-fill {{ $badge['icon'] }}" @if($badge['icon'] === 'ph-spinner-gap') style="animation: spin 2s linear infinite;" @endif></i> 
                                {{ $stageText }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            @if(!empty($partida->combinacion_codice) && is_array($partida->combinacion_codice))
                                <span title="Combinación Secreta" style="font-family:'Courier New',monospace; font-weight:700; color:#b45309; background:#fffbeb; border:1px solid #fcd34d; padding:2px 6px; border-radius:6px; font-size:0.85rem;">
                                    {{ implode('-', $partida->combinacion_codice) }}
                                </span>
                            @else
                                <span style="color:var(--text-muted); font-size:1.2rem; font-weight:bold;">—</span>
                            @endif
                        </td>
                        <td class="time-display">
                            @if($partida->fase_actual === 'FINALIZADO' || $partida->fase_actual === 'FINALIZADO_TIMEOUT')
                                @php
                                    $colorClass = $partida->fase_actual === 'FINALIZADO' ? 'text-success' : 'text-danger';
                                @endphp
                                <span class="{{ $colorClass }}">
                                    {{ floor((int)($partida->tiempo_segundos ?? 0) / 60) }}m {{ str_pad((int)($partida->tiempo_segundos ?? 0) % 60, 2, '0', STR_PAD_LEFT) }}s
                                </span>
                            @else
                                @if($partida->inicio_juego)
                                    @php
                                        $segs = abs(now()->diffInSeconds(\Carbon\Carbon::parse($partida->inicio_juego)));
                                    @endphp
                                    <span class="text-live">{{ floor($segs / 60) }}m {{ str_pad($segs % 60, 2, '0', STR_PAD_LEFT) }}s</span>
                                @else
                                    <span style="color:var(--text-muted)">--:--</span>
                                @endif
                            @endif
                        </td>
                        <td style="text-align:center; font-weight:600;">
                            @php $pistasUsadas = \App\Models\PistaUsada::where('partida_id', $partida->id)->count(); @endphp
                            @if($pistasUsadas > 0)
                                <span style="background:#fef08a; color:#854d0e; padding:4px 8px; border-radius:6px; font-size:0.85rem;">💡 {{ $pistasUsadas }}</span>
                            @else
                                <span style="color:var(--text-muted); font-size:0.85rem;">0</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <form action="/admin/equipo/{{ $partida->uuid }}" method="POST" onsubmit="return confirm('¿Borrar permanentemente el progreso de {{ $partida->nombre_equipo }}?');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action" title="Eliminar del sistema">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding: 4rem 2rem; color:var(--text-muted);">
                            <i class="ph ph-ghost" style="font-size:3rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>
                            Sistema vacío. Esperando a que los alumnos se registren.
                        </td>
                    </tr>
                    @endforelse
@endsection

@push('scripts')
<script>
    // Auto recarga cada 10 segundos
    setTimeout(() => {
        window.location.reload();
    }, 10000);
</script>

<!-- Chart.js para la Gráfica -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript externalizado del dashboard -->
<script src="{{ asset('js/admin-dashboard.js') }}?v={{ time() }}"></script>
@endpush
