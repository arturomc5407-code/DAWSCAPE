@extends('layouts.admin')

@push('admin-styles')
<style>
    .db-viewer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .db-viewer-header .badge-table {
        background: rgba(59, 130, 246, 0.1);
        color: var(--brand-color);
        padding: 6px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .db-table-wrapper {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background: var(--surface-color);
    }

    .db-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .db-table thead th {
        background: #f1f5f9;
        color: var(--text-main);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 2px solid var(--border-color);
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .db-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: background 0.15s;
    }

    .db-table tbody tr:hover {
        background: rgba(59, 130, 246, 0.03);
    }

    .db-table tbody tr:last-child {
        border-bottom: none;
    }

    .db-table tbody td {
        padding: 10px 16px;
        color: var(--text-main);
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .db-table tbody td.cell-null {
        color: #94a3b8;
        font-style: italic;
    }

    .db-pagination {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }

    .db-pagination nav {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .db-pagination .page-link {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid var(--border-color);
        color: var(--text-main);
        text-decoration: none;
        transition: all 0.2s;
    }

    .db-pagination .page-item.active .page-link {
        background: var(--brand-color);
        color: white;
        border-color: var(--brand-color);
    }

    .db-pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        pointer-events: none;
    }

    .db-empty {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }

    .db-empty i {
        font-size: 3rem;
        display: block;
        margin-bottom: 1rem;
        color: #cbd5e1;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: color 0.2s;
    }

    .btn-back:hover {
        color: var(--brand-color);
    }
</style>
@endpush

@section('admin-content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="ph ph-table" style="margin-right: 8px;"></i>{{ $table }}</h1>
        <p class="page-subtitle">Visor de datos — Solo lectura</p>
    </div>
    <a href="{{ route('admin.system') }}" class="btn-back">
        <i class="ph ph-arrow-left"></i> Volver a System Health
    </a>
</div>

<div class="glass-panel">
    <div class="db-viewer-header">
        <span class="badge-table">
            <i class="ph ph-database" style="margin-right: 4px;"></i>
            {{ $datos->total() }} {{ $datos->total() === 1 ? 'registro' : 'registros' }}
        </span>
        @if($datos->lastPage() > 1)
            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
                Página {{ $datos->currentPage() }} de {{ $datos->lastPage() }}
            </span>
        @endif
    </div>

    @if($datos->count() > 0)
        <div class="db-table-wrapper">
            <table class="db-table">
                <thead>
                    <tr>
                        @foreach(array_keys((array) $datos->first()) as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos as $row)
                        <tr>
                            @foreach((array) $row as $value)
                                <td class="{{ is_null($value) ? 'cell-null' : '' }}">
                                    {{ is_null($value) ? 'NULL' : e($value) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="db-pagination">
            {{ $datos->links() }}
        </div>
    @else
        <div class="db-empty">
            <i class="ph ph-empty"></i>
            <p>Esta tabla no contiene registros.</p>
        </div>
    @endif
</div>
@endsection
