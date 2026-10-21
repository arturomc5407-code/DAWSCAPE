<!--
====================================================================
   ___    ___   _      __ _____  ____   ___  ___   ____  ____
  / _ \  / _ \ | | /\ | |/ ___/ / ___| / _ \| _ \ | __/ |  _ \
 | | | || | | || |/  \| |\___ \ | |   | | | |  _/ | __| | | | |
 | |_| || |_| ||   /\   | __/ / | |___| |_| | |   | |___| |_| |
 \___/  \___/  |__/  \__|/___/   \____|\___/|_|   |____/|____/

====================================================================
  PROYECTO    : DAWSCAPE - Escape Room Educativo
  DESARROLLO  : Alumnos de Desarrollo de Aplicaciones Web (DAW)
  USO         : Uso exclusivamente educativo y de demostracion tecnica.
====================================================================
-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#3b82f6">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <title>@yield('title', 'DAWSCAPE')</title>

    <!-- Fuentes comunes para todo el proyecto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">

    <!-- Stacks para inyectar CSS especifico desde las vistas -->
    @stack('styles')

    {{-- Base URL para el sistema de audio (necesario para asset() en JS) --}}
    <script>
        window.AUDIO_BASE_URL = "{{ asset('sonido') }}";
    </script>

    {{-- Vite Assets (Critical for Audio System) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <!-- Contenido principal de la vista -->
    @yield('content')


    <!-- Stacks para inyectar JS especifico desde las vistas -->
    @stack('scripts')

    <!-- Firma discreta -->
    <div style="position: fixed; bottom: 5px; right: 10px; font-family: monospace; font-size: 0.65rem; color: rgba(255, 255, 255, 0.3); z-index: 9999; text-align: right;">
        DAWSCAPE SYSTEM // DAW <br>
        <a href="{{ route('legal') }}" style="color: rgba(255, 255, 255, 0.3); text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#00ff88'" onmouseout="this.style.color='rgba(255, 255, 255, 0.3)'" target="_blank">AVISO LEGAL Y RGPD</a>
    </div>
</body>
</html>
