@extends('layouts.admin')

@section('title', 'Gestión de QRs | DAWSCAPE Admin')

@push('admin-styles')
    <style>
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 0.8rem 1rem;
            font-size: 0.95rem;
            background-color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background-color: white;
        }
        .btn-primary-custom {
            background-color: var(--brand-color);
            color: white; border: none; font-weight: 600;
            padding: 0.8rem 1.5rem; border-radius: 8px;
            transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-primary-custom:hover { background-color: #2563eb; color: white; transform: translateY(-2px); }

        .card-qr {
            background: white; border: 1px solid var(--border-color); border-radius: 12px;
            padding: 1.5rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: all 0.2s;
        }
        .card-qr:hover { border-color: #cbd5e1; box-shadow: 0 8px 15px rgba(0,0,0,0.05); }

        .print-container { display: flex; align-items: center; gap: 1.5rem; }
        .qr-info h3 { font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.3rem; }
        .qr-info p { margin-bottom: 0; color: var(--text-muted); font-size: 0.9rem; }
        .qr-phase { margin-top: 0.6rem; display: inline-flex; align-items: center; gap: 5px; background: #fefce8; color: #a16207; border: 1px solid #fef08a; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; }
        .qr-free { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

        .actions { display: flex; gap: 0.5rem; flex-direction: column; }
        .btn-print { background: #f1f5f9; color: var(--text-main); border: 1px solid #cbd5e1; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
        .btn-print:hover { background: #e2e8f0; }
        .btn-delete { background: white; border: 1px solid #fecaca; color: #ef4444; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
        .btn-delete:hover { background: #fee2e2; border-color: #fca5a5; }

        @media print {
            body { background: white; }
            .page-header, form, .alert, .actions, .section-title, .admin-only { display: none !important; }
            .card-qr { display: none; margin: 0; border: none; box-shadow: none; }
            .card-qr.printing {
                display: flex; border: 2px dashed #000; margin-top: 2cm;
                flex-direction: column; text-align: center;
            }
            .card-qr.printing .print-container { flex-direction: column; justify-content: center; align-items: center; }
            .card-qr.printing .qr-info { margin-top: 1rem; }
            .card-qr.printing .qr-info h3 { font-size: 1.5rem; }
            .card-qr.printing .qr-info p { font-size: 1.2rem; color: #000; border-left: 4px solid #000; padding: 15px; margin-top: 10px; background: #f9f9f9; }
        }

        @media (max-width: 768px) {
            .card-qr { flex-direction: column; gap: 1rem; align-items: flex-start; }
            .print-container { flex-direction: column; }
            .actions { flex-direction: row; width: 100%; }
            .actions .btn-print, .actions .btn-delete { flex: 1; }
        }
    </style>
@endpush

@section('admin-content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Gestor de QRs</h1>
            <div class="page-subtitle">Genera e imprime puntos de control del Escape Room.</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center" style="background:#f0fdf4; border-color:#86efac; color:#166534; border-radius:12px; font-weight:500;">
            <i class="ph ph-check-circle me-2" style="font-size:1.5rem;"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center" style="background:#fef2f2; border-color:#fca5a5; color:#991b1b; border-radius:12px; font-weight:500;">
            <i class="ph ph-warning me-2" style="font-size:1.5rem;"></i> {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius:12px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-panel">
        <form action="/admin/qrs" method="POST">
            @csrf
            <h4 class="mb-4" style="font-weight: 700; color:var(--text-main);"><i class="ph ph-plus-circle" style="color:var(--brand-color)"></i> Generar Nuevo Puesto QR</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color:var(--text-muted); font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Nombre del QR / Título</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Máquina de café planta 1" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color:var(--text-muted); font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Vinculación de Fase</label>
                    <select name="fase_requerida" class="form-select" required>
                        <option value="" disabled selected>Selecciona una opción...</option>
                        <option value="Prueba 3 Angel">Prueba 3 (Máquina de Café)</option>
                        <option value="Prueba 4 Arturo">Prueba 4 (Máquina Oasis Snacks)</option>
                        <option value="Prueba 5 Eloy">Prueba 5 (Protocolo Espejo - Taquillas)</option>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold" style="color:var(--text-muted); font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Instrucción de Ubicación Fija</label>
                <textarea name="descripcion" class="form-control" rows="2" placeholder="Ej: Pegar en el lateral derecho de la máquina..." required></textarea>
            </div>
            <button type="submit" class="btn-primary-custom"><i class="ph ph-qr-code"></i> Generar Adhesivo QR</button>
        </form>
    </div>

    <h2 style="font-size:1.3rem; font-weight:700; margin-bottom:1.2rem; margin-top:2.5rem; color:var(--text-main);">Inventario de QRs Activos</h2>

    @if($qrs->isEmpty())
        <div style="text-align:center; padding: 4rem 2rem; color:var(--text-muted); background: white; border-radius: 16px; border: 1px dashed #cbd5e1;">
            <i class="ph ph-qr-code" style="font-size:3rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>
            No hay ningún QR en el sistema. Genera el primero arriba.
        </div>
    @else
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            @foreach($qrs as $qr)
            <div class="card-qr" id="qr-card-{{ $qr->id }}">
                <div class="print-container">
                    <div style="background:white; padding:10px; border-radius:10px; border:1px solid #f1f5f9; box-shadow:0 2px 5px rgba(0,0,0,0.02)">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->color(0, 0, 0)->margin(0)->generate($qr->url) !!}
                    </div>
                    <div class="qr-info">
                        <h3>{{ $qr->nombre }}</h3>
                        <p>{{ Str::limit($qr->descripcion, 60) }}</p>
                        @if($qr->fase_requerida)
                            <div class="qr-phase"><i class="ph-fill ph-lock-key"></i> Fase: {{ $qr->fase_requerida }}</div>
                        @else
                            <div class="qr-phase qr-free"><i class="ph-fill ph-globe"></i> Acceso Libre</div>
                        @endif
                    </div>
                </div>
                <div class="actions">
                    <button class="btn-print" onclick="editarQr('{{ $qr->id }}', '{{ addslashes($qr->nombre) }}')"><i class="ph ph-pencil"></i> Editar</button>
                    <button class="btn-print" onclick="printQr('{{ $qr->id }}')"><i class="ph ph-printer"></i> Imprimir</button>
                    <form action="/admin/qrs/{{ $qr->id }}" method="POST" onsubmit="return confirm('¿Eliminar QR? Este código dejará de funcionar permanentemente.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" style="width: 100%;"><i class="ph ph-trash"></i> Eliminar</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <!-- Modal para editar nombre del QR -->
    <div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:white; padding:2rem; border-radius:12px; max-width:500px; width:90%;">
            <h3 style="margin-bottom:1.5rem; font-weight:700;">Editar Nombre del QR</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="text" id="editNombre" name="nombre" class="form-control mb-3" placeholder="Nuevo nombre" required>
                <div style="display:flex; gap:1rem; justify-content:flex-end;">
                    <button type="button" onclick="cerrarModal()" style="padding:0.6rem 1.2rem; border:1px solid #cbd5e1; background:white; border-radius:8px; cursor:pointer;">Cancelar</button>
                    <button type="submit" class="btn-primary-custom">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function printQr(id) {
            document.querySelectorAll('.card-qr').forEach(el => el.classList.remove('printing'));
            document.getElementById('qr-card-' + id).classList.add('printing');
            window.print();
        }

        function editarQr(id, nombreActual) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            const input = document.getElementById('editNombre');

            form.action = '/admin/qrs/' + id + '/editar';
            input.value = nombreActual;
            modal.style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('editModal')?.addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });
    </script>
@endsection
