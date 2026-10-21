@extends('layouts.admin')

@section('title', 'Banco de Preguntas | DAWSCAPE')

@push('admin-styles')
    <style>
        /* --- ESTILOS COMPARTIDOS PREMIUM --- */
        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; }
        .page-title { margin: 0; font-weight: 700; color: var(--text-main); font-size: 1.8rem; }
        .page-subtitle { color: var(--text-muted); margin-top: 5px; font-size: 0.95rem; }

        .glass-panel {
            background: var(--surface-color);
            border-radius: 16px;
            padding: 1.8rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            margin-bottom: 1.5rem;
            height: 100%;
        }

        .panel-heading {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .panel-heading i { color: var(--brand-color); font-size: 1.3rem; }

        /* --- FORMULARIO --- */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1.2rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: #f8fafc;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--brand-color);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
        }

        /* --- TABLA --- */
        .table-responsive {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        .table-custom { width: 100%; margin: 0; border-collapse: collapse; }
        .table-custom th {
            background-color: #f8fafc; color: var(--text-muted); font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.5px; font-size: 0.75rem; padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--border-color);
            text-align: left;
        }
        .table-custom td { padding: 1.2rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; color: var(--text-main); }
        .table-custom tr:hover { background-color: #f8fafc; }

        .table-custom tr.inactive td {
            opacity: 0.6;
            background-color: #f8fafc;
        }

        /* Acciones y Badges */
        .status-pill {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px;
            font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;
        }
        .status-active { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .status-inactive { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }

        .btn-action {
            width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid transparent; cursor: pointer; transition: all 0.2s; background: #f1f5f9;
        }

        .btn-toggle-on { color: #10b981; }
        .btn-toggle-on:hover { background: #d1fae5; }

        .btn-toggle-off { color: #94a3b8; }
        .btn-toggle-off:hover { background: #e2e8f0; color: #64748b; }

        .btn-delete { color: #ef4444; }
        .btn-delete:hover { background: #fee2e2; }

        .btn-edit { color: #f59e0b; }
        .btn-edit:hover { background: #fef3c7; }

        .answer-text {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 700;
            color: var(--brand-color);
            background: #eff6ff;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        /* Contenedor Grid Principal */
        .split-grid {
            display: grid;
            grid-template-columns: 1fr 2.5fr;
            gap: 1.5rem;
        }


        /* ========== PREGUNTAS — MOBILE CARDS ========== */
        @media (max-width: 768px) {
            .split-grid { grid-template-columns: 1fr; }
            .glass-panel { padding: 1rem; height: auto; }
            .panel-heading { font-size: 1rem; margin-bottom: 1rem; padding-bottom: 0.75rem; }

            /* --- Tabla → Tarjetas apiladas --- */
            .table-responsive {
                border: none;
                box-shadow: none;
                border-radius: 0;
                background: transparent;
                overflow: visible;
            }

            .table-custom thead { display: none; }

            .table-custom,
            .table-custom tbody {
                display: block;
                width: 100%;
            }

            .table-custom tr {
                display: block;
                background: #ffffff;
                border: 1px solid var(--border-color);
                border-radius: 12px;
                padding: 1rem;
                margin-bottom: 0.75rem;
                box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            }

            .table-custom tr:hover {
                background: #ffffff;
            }

            .table-custom tr.inactive {
                opacity: 0.55;
                background: #f8fafc;
            }

            .table-custom td {
                display: block;
                padding: 0.4rem 0;
                border-bottom: none;
                text-align: left !important;
                white-space: normal !important;
                font-size: 0.9rem;
                line-height: 1.5;
            }

            /* Etiquetas para cada campo */
            .table-custom td:nth-child(1)::before {
                content: '';
            }
            .table-custom td:nth-child(2)::before {
                content: 'Enunciado';
                display: block;
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--text-muted);
                margin-bottom: 4px;
            }
            .table-custom td:nth-child(3)::before {
                content: 'Respuestas';
                display: block;
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--text-muted);
                margin-bottom: 4px;
                margin-top: 8px;
            }

            /* Estado: ponerlo arriba como badge */
            .table-custom td:nth-child(1) {
                padding-bottom: 0.5rem;
                margin-bottom: 0.25rem;
                border-bottom: 1px solid #f1f5f9;
            }

            /* Enunciado: texto grande y legible */
            .table-custom td:nth-child(2) {
                font-size: 0.95rem;
                font-weight: 500;
                padding-bottom: 0.5rem;
            }

            /* Respuestas */
            .table-custom td:nth-child(3) {
                padding-bottom: 0.75rem;
            }

            /* Acciones: fila de botones grandes */
            .table-custom td:nth-child(4) {
                display: flex;
                gap: 8px;
                padding-top: 0.75rem;
                border-top: 1px solid #f1f5f9;
                justify-content: flex-start;
            }

            /* Botones de acción mucho más grandes para touch */
            .table-custom .btn-action {
                width: 44px;
                height: 44px;
                border-radius: 10px;
                font-size: 1.2rem;
                border: 1px solid var(--border-color);
            }

            .table-custom .btn-action i {
                font-size: 1.3rem;
            }

            /* Answer text más legible */
            .answer-text {
                font-size: 0.85rem;
                padding: 5px 10px;
            }

            /* Status pill */
            .status-pill {
                font-size: 0.7rem;
                padding: 4px 10px;
            }

            /* Formulario mejorado */
            .form-control {
                padding: 0.8rem 1rem;
                font-size: 16px; /* ≥16px evita zoom en iOS Safari */
                -webkit-appearance: none; /* Reset estilo Safari iOS */
                appearance: none;
            }

            textarea.form-control {
                min-height: 80px;
                -webkit-appearance: none;
                appearance: none;
            }

            .btn-submit {
                padding: 0.9rem;
                font-size: 0.95rem;
                -webkit-appearance: none;
                appearance: none;
            }

            /* Header responsive */
            .page-header {
                -webkit-flex-direction: column;
                flex-direction: column;
                -webkit-align-items: flex-start;
                align-items: flex-start;
                gap: 0.75rem;
                margin-bottom: 1.5rem;
            }
            .page-title { font-size: 1.3rem; }
            .page-subtitle { font-size: 0.85rem; }

            /* Acciones: flex con webkit */
            .table-custom td:nth-child(4) {
                display: -webkit-flex;
                display: flex;
                -webkit-flex-wrap: wrap;
                flex-wrap: wrap;
            }
        }
    </style>
@endpush

@section('admin-content')
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="ph ph-exam" style="color:var(--brand-color); margin-right:8px;"></i> Banco de Preguntas</h1>
            <div class="page-subtitle">Añade o modifica las preguntas de cultura general que aparecerán en la Prueba Final (Fase 7).</div>
        </div>
        <div>
            <div class="status-pill status-active" style="font-size: 0.85rem; padding: 8px 16px;">
                <i class="ph-fill ph-database"></i> Total Activas: {{ $preguntas->where('activa', true)->count() }}
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center" style="background:#f0fdf4; border-color:#86efac; color:#166534; border-radius:12px; font-weight:500; margin-bottom: 2rem; padding: 1rem 1.5rem; border: 1px solid #86efac;">
            <i class="ph ph-check-circle" style="font-size:1.5rem; margin-right: 12px;"></i> {{ session('success') }}
        </div>
    @endif

    <div class="split-grid">

        <!-- Formulario Izquierda -->
        <div>
            <div class="glass-panel">
                <div class="panel-heading">
                    <i class="ph ph-plus-circle"></i> Añadir Pregunta
                </div>

                <form action="{{ url('/preguntas') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Enunciado o Acertijo</label>
                        <textarea name="enunciado" class="form-control" required placeholder="Ej: ¿En qué año cayó el muro de Berlín?"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Respuesta principal</label>
                        <input type="text" name="respuesta_correcta" class="form-control" required placeholder="Ej: París">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Respuestas alternativas</label>
                        <input type="text" name="respuestas_alternativas" class="form-control" placeholder="Ej: paris, París, la ciudad de la luz">
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 6px; display: block; line-height: 1.4;">
                            * Separa cada alternativa con una coma. Se compararán <strong>sin tildes ni mayúsculas</strong>.
                        </small>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="ph ph-floppy-disk"></i> Guardar en BBDD
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabla Derecha -->
        <div>
            <div class="glass-panel">
                <div class="panel-heading" style="justify-content: space-between;">
                    <div><i class="ph ph-list-bullets"></i> Inventario de Pruebas</div>
                </div>



                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th style="width: 10%">Estado</th>
                                <th style="width: 40%">Enunciado</th>
                                <th style="width: 35%">Respuestas</th>
                                <th style="width: 15%; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($preguntas as $p)
                                <tr class="{{ !$p->activa ? 'inactive' : '' }}">
                                    <td>
                                        @if($p->activa)
                                            <span class="status-pill status-active"><i class="ph-fill ph-check-circle"></i> ACTIVA</span>
                                        @else
                                            <span class="status-pill status-inactive"><i class="ph-fill ph-minus-circle"></i> INACTIVA</span>
                                        @endif
                                    </td>
                                    <td style="line-height: 1.5;">{{ $p->enunciado }}</td>
                                    <td>
                                        <span class="answer-text">{{ $p->respuesta_correcta }}</span>
                                        @if($p->respuestas_alternativas && count($p->respuestas_alternativas) > 0)
                                            <div style="margin-top: 6px; display: flex; flex-wrap: wrap; gap: 4px;">
                                                @foreach($p->respuestas_alternativas as $alt)
                                                    <span style="display: inline-block; background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; border: 1px solid #e2e8f0;">{{ $alt }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">

                                        <!-- Botón Editar -->
                                        <a href="{{ route('admin.pregunta.edit', $p->id) }}" class="btn-action btn-edit" title="Editar pregunta" style="display:inline-flex; text-decoration:none; margin-right: 4px;">
                                            <i class="ph ph-pencil-simple" style="font-size:1.2rem;"></i>
                                        </a>

                                        <!-- Botón Toggle Estado -->
                                        <form action="{{ url('/preguntas/' . $p->id . '/toggle') }}" method="POST" style="display:inline-block; margin:0;">
                                            @csrf
                                            <button type="submit" class="btn-action {{ $p->activa ? 'btn-toggle-on' : 'btn-toggle-off' }}" title="{{ $p->activa ? 'Desactivar' : 'Activar' }}">
                                                <i class="ph {{ $p->activa ? 'ph-toggle-right' : 'ph-toggle-left' }}" style="font-size:1.4rem;"></i>
                                            </button>
                                        </form>

                                        <!-- Botón Borrar -->
                                        <form action="{{ url('/preguntas/' . $p->id) }}" method="POST" onsubmit="return confirm('¿Borrar definitivamente esta pregunta?');" style="display:inline-block; margin:0; margin-left: 4px;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="Eliminar del sistema">
                                                <i class="ph ph-trash" style="font-size:1.2rem;"></i>
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; padding: 4rem 2rem; color:var(--text-muted);">
                                        <i class="ph ph-database" style="font-size:3rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>
                                        El banco de preguntas está completamente vacío.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </di       });
    })();
    </script>
@endsection
