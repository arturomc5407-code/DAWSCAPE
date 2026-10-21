<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAWSCAPE | Inscripción Exitosa</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">

    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --green: #00ff88;
            --cyan: #67e8f9;
            --bg: #020c06;
            --panel: #041a0e;
            --border: #00ff8840;
            --text: #c8ffdf;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(ellipse at top, #041a0e 0%, #020c06 55%, #000 100%);
            color: var(--text);
            font-family: 'Share Tech Mono', monospace;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0, 255, 136, 0.04) 2px,
                rgba(0, 255, 136, 0.04) 4px
            );
            pointer-events: none;
            opacity: 0.5;
        }

        .container {
            width: 100%;
            max-width: 620px;
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .card {
            background: var(--panel);
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow:
                0 0 30px rgba(0, 255, 136, 0.28),
                0 0 80px rgba(0, 255, 136, 0.12);
            padding: 1.75rem 1.5rem 1.5rem;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 1px solid rgba(0, 255, 136, 0.14);
            pointer-events: none;
            box-shadow:
                0 0 25px rgba(0, 255, 136, 0.32),
                inset 0 0 25px rgba(0, 0, 0, 0.9);
        }

        .card-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .card-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: 0.18em;
            color: var(--green);
            text-shadow:
                0 0 20px rgba(0, 255, 136, 0.9),
                0 0 60px rgba(0, 255, 136, 0.4);
            margin-bottom: 0.2rem;
        }

        .card-header p {
            font-size: 0.8rem;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: #7cf0b8;
            opacity: 0.9;
        }

        .card-body {
            padding-top: 0.75rem;
        }

        .success-banner {
            border-radius: 10px;
            border: 1px solid rgba(0, 255, 136, 0.55);
            background: radial-gradient(circle at top left, rgba(0, 255, 136, 0.16), transparent 55%);
            padding: 1.1rem 1rem;
            margin-bottom: 1.4rem;
            text-align: center;
        }

        .success-banner h2 {
            font-size: 0.95rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--green);
            margin-bottom: 0.4rem;
        }

        .success-banner p {
            font-size: 0.85rem;
            color: var(--text);
        }

        .team-info {
            border-radius: 10px;
            border: 1px solid rgba(0, 255, 136, 0.35);
            background: rgba(0, 0, 0, 0.6);
            padding: 1rem 0.9rem;
            margin-bottom: 1.2rem;
        }

        .team-info h3 {
            font-size: 0.78rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--green);
            margin-bottom: 0.7rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.45rem 0;
            border-bottom: 1px dashed rgba(0, 255, 136, 0.22);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 0.8rem;
            color: var(--text);
            opacity: 0.85;
        }

        .info-value {
            font-size: 0.85rem;
            color: var(--green);
            font-weight: 700;
            white-space: nowrap;
        }

        .info-value.code {
            font-family: 'Share Tech Mono', monospace;
            letter-spacing: 0.18em;
            font-size: 1rem;
            text-shadow: 0 0 12px rgba(0, 255, 136, 0.7);
        }

        .instructions {
            border-radius: 10px;
            border: 1px solid rgba(103, 232, 249, 0.4);
            background: rgba(1, 10, 8, 0.9);
            padding: 1rem 0.9rem;
            margin-bottom: 1.2rem;
        }

        .instructions h3 {
            font-size: 0.78rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--cyan);
            margin-bottom: 0.7rem;
        }

        .instructions ol {
            margin-left: 1.2rem;
            font-size: 0.85rem;
            color: var(--text);
            line-height: 1.7;
        }

        .instructions li {
            margin-bottom: 0.35rem;
        }

        .qr-section {
            border-radius: 10px;
            border: 1px dashed rgba(0, 255, 136, 0.35);
            background: rgba(2, 12, 6, 0.85);
            padding: 1.2rem 0.9rem;
            text-align: center;
            margin-bottom: 1.2rem;
        }

        .qr-section h3 {
            font-size: 0.78rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--green);
            margin-bottom: 0.9rem;
        }

        .qr-container {
            display: inline-block;
            background: #000;
            padding: 0.75rem;
            border-radius: 10px;
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.35);
            margin-bottom: 0.8rem;
        }

        .qr-container img {
            width: 230px;
            height: 230px;
            display: block;
        }

        .qr-description {
            font-size: 0.8rem;
            color: var(--text);
            opacity: 0.9;
            max-width: 100%;
            margin: 0 auto;
        }

        .button-group {
            display: flex;
            justify-content: center;
            margin-top: 1.2rem;
        }

        .btn {
            padding: 0.85rem 1.8rem;
            font-size: 0.85rem;
            font-family: 'Share Tech Mono', monospace;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            border: 1px solid var(--green);
            color: var(--green);
            background: transparent;
            box-shadow: 0 0 18px rgba(0, 255, 136, 0.4);
        }

        .btn:hover {
            background: rgba(0, 255, 136, 0.16);
            box-shadow: 0 0 26px rgba(0, 255, 136, 0.8);
            transform: translateY(-1px);
        }

        @media (max-width: 640px) {
            .container {
                padding: 1rem;
            }

            .card {
                padding: 1.4rem 1.1rem 1.1rem;
            }

            .card-header h1 {
                font-size: 1.7rem;
            }

            .qr-container img {
                width: 200px;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="card-header">
                <h1>DAWSCAPE</h1>
                <p>Inscripción Completada</p>
            </div>

            <!-- Body -->
            <div class="card-body">
                <!-- Success Banner -->
                <div class="success-banner">
                    <h2>Equipo Registrado Correctamente</h2>
                    <p>Tu equipo está listo para participar en la actividad</p>
                </div>

                @if(session('correo_fallido_codigo'))
                <div style="border-radius:10px; border:1px solid rgba(255,170,0,0.6); background:rgba(255,170,0,0.1); padding:1.1rem 1rem; margin-bottom:1.4rem; text-align:center;">
                    <div style="font-family:'Orbitron',sans-serif; font-size:0.85rem; color:#ffaa00; margin-bottom:0.5rem; text-transform:uppercase; letter-spacing:0.12em;">
                        ⚠️ No se pudo enviar el correo de confirmación
                    </div>
                    <p style="font-size:0.85rem; color:#c8ffdf; margin-bottom:0.75rem;">
                        Tu inscripción está guardada correctamente, pero el correo no llegó por un problema técnico. <strong>Anota ahora tu código de acceso:</strong>
                    </p>
                    <div style="font-family:'Share Tech Mono',monospace; font-size:1.8rem; letter-spacing:0.3em; color:#ffaa00; text-shadow:0 0 15px rgba(255,170,0,0.6); font-weight:700;">
                        {{ session('correo_fallido_codigo') }}
                    </div>
                </div>
                @endif

                <!-- Team Information -->
                <div class="team-info">
                    <h3>Datos del Equipo</h3>
                    <div class="info-row">
                        <span class="info-label">Nombre del Equipo:</span>
                        <span class="info-value">{{ $inscripcion->nombre_equipo }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Correo Electrónico:</span>
                        <span class="info-value">{{ $inscripcion->correo }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Código de Acceso:</span>
                        <span class="info-value" style="font-size: 1.3rem; letter-spacing: 2px;">{{ $inscripcion->codigo_acceso }}</span>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="instructions">
                    <h3>Instrucciones Importantes</h3>
                    <ol>
                        <li><strong>Guarda tu código:</strong> Tu código de acceso es <strong>{{ $inscripcion->codigo_acceso }}</strong>. Cópialo y guárdalo en un lugar seguro.</li>
                        <li><strong>Revisa tu correo:</strong> Te hemos enviado tu código de acceso a <strong>{{ $inscripcion->correo }}</strong>. Verifica la bandeja de entrada.</li>
                        <li><strong>Atiende al comunicado:</strong> El equipo organizador te contactará con los detalles finales del evento.</li>
                        <li><strong>El día del evento:</strong> Integra tu código + nombre de equipo para acceder al Escape Room digital.</li>
                        <li><strong>Trabaja en equipo:</strong> La colaboración es fundamental para completar la actividad.</li>
                    </ol>
                </div>

                <!-- Buttons -->
                <div class="button-group">
                    <a href="{{ route('inicio') }}" class="btn btn-secondary">
                        Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
