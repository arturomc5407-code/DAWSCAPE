@extends('layouts.admin')

@section('title', 'Editar Pregunta | DAWSCAPE')

@push('admin-styles')
    <style>
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
            max-width: 660px;
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

        .form-group { margin-bottom: 1.5rem; }

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
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-back {
            padding: 1rem 2rem;
            background: #f1f5f9;
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 1rem;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #e2e8f0;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .error-text {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 4px;
        }
    </style>
@endpush

@section('admin-content')
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="ph ph-pencil-simple" style="color:var(--brand-color); margin-right:8px;"></i> Editar Pregunta</h1>
            <div class="page-subtitle">Modifica el enunciado o las respuestas válidas de esta pregunta.</div>
        </div>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; border-radius:12px; padding:1rem 1.5rem; margin-bottom:2rem; font-weight:500;">
            <i class="ph ph-warning-circle" style="font-size:1.3rem; margin-right:8px;"></i>
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <div class="glass-panel">
        <div class="panel-heading">
            <i class="ph ph-pencil-simple"></i> Datos de la Pregunta #{{ $pregunta->id }}
        </div>

        <form action="{{ route('admin.pregunta.update', $pregunta->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Enunciado o Acertijo</label>
                <textarea name="enunciado" class="form-control" required>{{ old('enunciado', $pregunta->enunciado) }}</textarea>
                @error('enunciado')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Respuesta principal</label>
                <input type="text" name="respuesta_correcta" class="form-control" required value="{{ old('respuesta_correcta', $pregunta->respuesta_correcta) }}">
                @error('respuesta_correcta')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Respuestas alternativas</label>
                <input type="text" name="respuestas_alternativas" class="form-control" value="{{ old('respuestas_alternativas', implode(', ', $pregunta->respuestas_alternativas ?? [])) }}" placeholder="Ej: paris, París, la ciudad de la luz">
                <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 6px; display: block; line-height: 1.4;">
                    * Separa cada alternativa con una coma. Se compararán <strong>sin tildes ni mayúsculas</strong>.
                </small>
                @error('respuestas_alternativas')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="ph ph-floppy-disk"></i> Guardar Cambios
                </button>
                <a href="{{ route('admin.preguntas') }}" class="btn-back">
                    <i class="ph ph-arrow-left"></i> Volver
                </a>
            </div>
        </form>
    </div>
@endsection
