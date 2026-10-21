@extends('layouts.admin')

@section('title', 'Trazabilidad de Equipo | DAWSCAPE')

@push('admin-styles')
    @vite(['resources/css/admin.css'])
@endpush

@section('admin-content')
<div class="page-header">
    <div>
        <h1 class="page-title">Trazabilidad de Equipo</h1>
        <div class="page-subtitle">Logs de auditoría para el equipo: {{ $partida->nombre_equipo }}</div>
    </div>
    <div>
        <a href="{{ route('dashboard') }}" class="export-btn" style="background:var(--surface-hover); color:var(--text-main); border: 1px solid var(--border-color);">
            <i class="ph ph-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="glass-panel" style="margin-top: 2rem;">
    <div class="panel-heading"><i class="ph ph-scroll"></i> Línea de Tiempo de Acciones</div>
    
    @if($partida->logs->isEmpty())
        <div style="text-align:center; padding: 2rem; color:var(--text-muted);">
            <i class="ph ph-ghost" style="font-size:3rem; opacity:0.3; display:block; margin-bottom:1rem;"></i>
            Este equipo no tiene actividad registrada aún.
        </div>
    @else
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 20%">Fecha / Hora</th>
                        <th style="width: 25%">Fase / Prueba</th>
                        <th style="width: 20%">Acción Registrada</th>
                        <th style="width: 35%">Detalles (JSON)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($partida->logs as $log)
                        <tr class="team-row">
                            <td style="color:var(--text-muted); font-weight:600;">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td>
                                <span style="background:#f1f5f9; padding:4px 10px; border-radius:6px; font-size:0.85rem; font-weight:600; color:var(--text-muted);">
                                    {{ $log->fase }}
                                </span>
                            </td>
                            <td>
                                @if($log->accion === 'Entra a la prueba')
                                    <span class="status-pill status-warning"><i class="ph-fill ph-door-open"></i> {{ $log->accion }}</span>
                                @elseif($log->accion === 'Intento fallido')
                                    <span class="status-pill status-danger"><i class="ph-fill ph-x-circle"></i> {{ $log->accion }}</span>
                                @elseif($log->accion === 'Prueba superada')
                                    <span class="status-pill status-success"><i class="ph-fill ph-check-circle"></i> {{ $log->accion }}</span>
                                @else
                                    <span class="status-pill" style="background:#f1f5f9; color:var(--text-muted)"><i class="ph-fill ph-info"></i> {{ $log->accion }}</span>
                                @endif
                            </td>
                            <td style="font-family: monospace; font-size: 0.85rem; color: var(--text-muted);">
                                @if($log->detalles)
                                    <pre style="margin:0; background:rgba(0,0,0,0.05); padding: 5px; border-radius:4px;">{{ json_encode($log->detalles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
