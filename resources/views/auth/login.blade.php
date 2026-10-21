@extends('layouts.app')

@section('title', 'Acceso Admin - DAWSCAPE')

@push('styles')
<style>
    html, body {
        margin: 0; padding: 0; width: 100%; height: 100%;
    }
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(ellipse at center, #1e293b 0%, #0f172a 100%);
        font-family: 'Inter', sans-serif;
    }

    .login-wrap {
        width: 100%;
        max-width: 420px;
        padding: 24px;
    }

    .login-card {
        background: rgba(30, 41, 59, 0.85);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 20px;
        padding: 44px 40px;
        box-shadow: 0 30px 60px -15px rgba(0,0,0,0.6);
    }

    .login-logo {
        text-align: center;
        margin-bottom: 32px;
    }

    .login-logo h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 2rem;
        font-weight: 900;
        letter-spacing: 3px;
        background: linear-gradient(135deg, #60a5fa, #a78bfa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0 0 6px 0;
    }

    /* MEJORADO: Logo p con mejor contraste */
    .login-logo p {
        color: #cbd5e1;
        font-size: 0.85rem;
        margin: 0;
        text-shadow: 0 0 4px rgba(203, 213, 225, 0.1);
    }

    .form-group {
        margin-bottom: 18px;
    }

    /* MEJORADO: Form label con mejor contraste */
    .form-group label {
        display: block;
        color: #cbd5e1;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        box-sizing: border-box;
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        padding: 13px 16px;
        color: #f1f5f9;
        font-size: 1rem;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    /* MEJORADO: Placeholder con mejor contraste */
    .form-control::placeholder { color: #64748b; }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .error-box {
        background: rgba(239,68,68,0.1);
        border-left: 3px solid #ef4444;
        padding: 10px 14px;
        border-radius: 8px;
        margin-bottom: 18px;
        color: #fca5a5;
        font-size: 0.85rem;
    }

    .remember-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
    }

    .remember-row input[type=checkbox] { accent-color: #3b82f6; }
    /* MEJORADO: Remember label con mejor contraste */
    .remember-row label { color: #cbd5e1; font-size: 0.8rem; cursor: pointer; }

    .btn-login {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        letter-spacing: 0.5px;
        transition: all 0.25s;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(139,92,246,0.4);
        filter: brightness(1.1);
    }

    .footer-link {
        text-align: center;
        margin-top: 24px;
    }
    /* MEJORADO: Footer links con mejor contraste */
    .footer-link a {
        color: #94a3b8;
        font-size: 0.8rem;
        text-decoration: none;
        transition: color 0.2s;
    }
    .footer-link a:hover { color: #cbd5e1; }
</style>
@endpush

@section('content')
<div class="login-wrap">
    <div class="login-card">

        <div class="login-logo">
            <h1>DAWSCAPE</h1>
            <p>Panel de Administración</p>
        </div>

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username"
                       class="form-control"
                       value="{{ old('username') }}"
                       required autofocus autocomplete="username"
                       placeholder="Tu usuario">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       class="form-control"
                       required autocomplete="current-password"
                       placeholder="••••••••">
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember_me" name="remember">
                <label for="remember_me">Mantener sesión iniciada</label>
            </div>

            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>

        <div class="footer-link">
            <a href="{{ route('inicio') }}">← Volver al inicio del juego</a>
        </div>
    </div>
</div>
@endsection
