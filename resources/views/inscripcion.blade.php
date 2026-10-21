<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAWSCAPE | Inscripción de Equipos</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- ICONOS -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');

        :root {
            --green: #00ff88;
            --red: #ff2244;
            --bg: #020c06;
            --panel: #041a0e;
            --border: #00ff8840;
            --text: #c8ffdf;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Share Tech Mono', monospace;
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,255,136,0.013) 2px, rgba(0,255,136,0.013) 4px);
            pointer-events: none;
            z-index: 1000;
        }

        .screen {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
            position: relative;
            z-index: 10;
        }

        .logo {
            font-family: 'Orbitron', sans-serif;
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 900;
            color: var(--green);
            text-shadow: 0 0 20px var(--green), 0 0 60px #00ff8840;
            letter-spacing: 0.2em;
            animation: pulse-glow 2.5s ease-in-out infinite;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .subtitle {
            font-size: 0.85rem;
            color: #00ff88;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            margin-bottom: 2rem;
            text-align: center;
            text-shadow: 0 0 10px rgba(0, 255, 136, 0.2);
        }

        .glitch-text {
            animation: blink 2s infinite;
            color: var(--red);
            font-size: 0.9rem;
            text-shadow: 0 0 10px var(--red);
            letter-spacing: 0.1em;
            text-align: center;
            margin-bottom: 2rem;
        }

        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

        .login-card {
            width: 100%;
            max-width: 500px;
            border: 1px solid var(--border);
            background: var(--panel);
            padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
            position: relative;
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.1);
        }

        .login-card::before {
            content: '// INSCRIPCIÓN';
            position: absolute;
            top: -0.7rem; left: 1rem;
            background: var(--bg);
            padding: 0 0.5rem;
            color: var(--green);
            font-size: 0.7rem;
            letter-spacing: 0.2em;
        }

        .form-group { margin-bottom: 1.8rem; }

        .form-label {
            display: block;
            font-size: 0.75rem;
            color: #00ff88;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            text-shadow: 0 0 6px rgba(0, 255, 136, 0.15);
        }

        .form-control {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 2px solid #00ff8840;
            color: var(--green);
            font-family: 'Share Tech Mono', monospace;
            font-size: max(16px, 1.1rem);
            padding: 0.8rem 0.5rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control::placeholder { color: #88ccaa60; }
        .form-control:focus { border-bottom-color: var(--green); box-shadow: 0 4px 15px -10px var(--green); }

        .helper-text {
            display: block;
            margin-top: 0.4rem;
            color: #88ccaa;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            text-shadow: 0 0 4px rgba(136, 204, 170, 0.15);
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        input[type="checkbox"] {
            appearance: none;
            background-color: transparent;
            margin: 0;
            margin-top: 0.15rem;
            font: inherit;
            color: currentColor;
            width: 1.25em;
            height: 1.25em;
            border: 1px solid var(--green);
            display: grid;
            place-content: center;
            cursor: pointer;
        }

        input[type="checkbox"]::before {
            content: "";
            width: 0.65em;
            height: 0.65em;
            transform: scale(0);
            transition: 120ms transform ease-in-out;
            box-shadow: inset 1em 1em var(--green);
            background-color: var(--green);
        }

        input[type="checkbox"]:checked::before { transform: scale(1); }

        .checkbox-label { font-size: 0.85rem; color: #669977; line-height: 1.5; cursor: pointer; }

        .btn {
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
            letter-spacing: 0.3em;
            padding: 1.2rem;
            border: 2px solid var(--green);
            background: transparent;
            color: var(--green);
            cursor: pointer;
            text-transform: uppercase;
            transition: all 0.25s;
            position: relative;
            overflow: hidden;
            width: 100%;
            font-weight: 700;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: var(--green);
            transition: left 0.3s;
            z-index: -1;
        }

        .btn:hover::before { left: 0; }
        .btn:hover { color: var(--bg); box-shadow: 0 0 20px var(--green); }
        .btn { min-height: 52px; -webkit-tap-highlight-color: transparent; }

        .btn-secondary {
            border-color: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 1.5rem;
        }

        .btn-secondary:hover { color: var(--green); }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid;
            font-size: 0.9rem;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.3);
            color: #86efac;
        }

        .alert-error {
            background: rgba(255, 107, 107, 0.1);
            border-color: rgba(255, 107, 107, 0.3);
            color: #ff9999;
        }

        .error-message {
            color: #ff6b6b;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        @keyframes pulse-glow {
            0%, 100% { text-shadow: 0 0 20px var(--green), 0 0 50px #00ff8860; }
            50% { text-shadow: 0 0 40px var(--green), 0 0 90px #00ff8840; }
        }

        /* Particles */
        .particles { position: fixed; inset: 0; pointer-events: none; z-index: 1; }
        .particle {
            position: absolute;
            width: 2px; height: 2px;
            background: var(--green);
            border-radius: 50%;
            opacity: 0;
            animation: float linear infinite;
        }
        @keyframes float {
            0% { opacity: 0; transform: translateY(100vh); }
            10% { opacity: 0.5; }
            90% { opacity: 0.3; }
            100% { opacity: 0; transform: translateY(-10vh); }
        }

        /* MOBILE */
        @media (max-width: 600px) {
            .screen { padding: 1rem; padding-top: 1.5rem; }
            .login-card { padding: 1.2rem 1rem; }
            .form-control { font-size: max(16px, 1rem); }
            .btn { font-size: 0.85rem; letter-spacing: 0.15em; }
        }
    </style>
</head>
<body>

@include('components.game-audio-setup', ['trackKey' => 'prueba10'])

<div class="particles" id="particles"></div>

<div class="screen">
  <div class="logo">DAWSCAPE</div>
  <div class="subtitle">// INSCRIPCIÓN DE EQUIPOS</div>
  <div class="glitch-text">COMPLETA LOS DATOS Y REGISTRA TU EQUIPO</div>

  <div style="max-width: 500px; margin-bottom: 2rem; padding: 1.5rem; background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 0.5rem; color: #cbd5e1; text-align: center; font-size: 0.95rem; line-height: 1.6;">
    <p style="margin-bottom: 1rem;"><strong style="color: #22c55e;">IMPORTANTE:</strong> Utiliza un correo del dominio <code>@estudiante.xtart.com</code> para poder acceder después.</p>
    <p>Una vez inscrito recibirás tus credenciales por email y podrás iniciar la experiencia desde cualquier dispositivo.</p>
  </div>

  <div class="login-card">
    <form method="POST" action="{{ route('inscripcion.guardar') }}">
      @csrf

      @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
      @endif

      @if ($errors->any())
        <div class="alert alert-error">
          <strong>⚠ Se encontraron errores:</strong>
          <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="form-group">
        <label class="form-label" for="nombre_equipo">Nombre del Equipo *</label>
        <input type="text" name="nombre_equipo" id="nombre_equipo" class="form-control" placeholder="Ej: Los Detectives Digitales" value="{{ old('nombre_equipo') }}" required maxlength="60">
        @error('nombre_equipo')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="correo">Correo Electrónico *</label>
        <input type="email" name="correo" id="correo" class="form-control" placeholder="ejemplo@estudiante.xtart.com" value="{{ old('correo') }}" required maxlength="120">
        @error('correo')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="ciclo">Ciclo *</label>
        <select id="ciclo" name="ciclo" class="form-control" required>
            <option value="">-- Selecciona tu ciclo --</option>
            @foreach ($ciclosGrupos as $ciclo => $grupos)
                <option value="{{ $ciclo }}" @if(old('ciclo') === $ciclo) selected @endif>{{ $ciclo }}</option>
            @endforeach
        </select>
        @error('ciclo')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="grupo">Grupo *</label>
        <select id="grupo" name="grupo" class="form-control" required disabled>
            <option value="">Primero selecciona tu ciclo</option>
        </select>
        @error('grupo')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <div class="checkbox-group">
        <input type="checkbox" id="acepta_terminos" name="acepta_terminos" value="1" required>
        <label class="checkbox-label" for="acepta_terminos">He leído y acepto el <a href="{{ route('legal') }}" target="_blank">Aviso Legal</a>. Entiendo que participo bajo mi propia responsabilidad.</label>
      </div>
      @error('acepta_terminos')
        <div class="error-message">{{ $message }}</div>
      @enderror

      <button type="submit" class="btn">Registrar Equipo</button>

      <a href="{{ route('inicio') }}" class="btn btn-secondary">Volver</a>
    </form>
  </div>
</div>

<script>
// Mapa ciclo → grupos (inyectado desde PHP)
const ciclosGrupos = @json($ciclosGrupos);

const cicloSelect = document.getElementById('ciclo');
const grupoSelect = document.getElementById('grupo');
const oldGrupo = @json(old('grupo', ''));

cicloSelect.addEventListener('change', function() {
    const ciclo = this.value;
    grupoSelect.innerHTML = '';

    if (!ciclo) {
        grupoSelect.disabled = true;
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'Primero selecciona tu ciclo';
        grupoSelect.appendChild(opt);
        return;
    }

    grupoSelect.disabled = false;
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = '-- Selecciona tu grupo --';
    grupoSelect.appendChild(placeholder);

    const grupos = ciclosGrupos[ciclo] || [];
    grupos.forEach(function(g) {
        const opt = document.createElement('option');
        opt.value = g;
        opt.textContent = g;
        if (g === oldGrupo) opt.selected = true;
        grupoSelect.appendChild(opt);
    });
});

// Restaurar estado si hay old() values (tras error de validación)
if (cicloSelect.value) {
    cicloSelect.dispatchEvent(new Event('change'));
}

// Partículas
(function() {
  const c = document.getElementById('particles');
  for (let i = 0; i < 25; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    p.style.left = Math.random() * 100 + '%';
    p.style.animationDuration = (10 + Math.random() * 14) + 's';
    p.style.animationDelay = (Math.random() * 12) + 's';
    c.appendChild(p);
  }
})();
</script>

<!-- Firma discreta -->
<div style="
    position: fixed;
    inset-inline: 0;
    bottom: 4px;
    padding: 0 8px;
    font-family: monospace;
    font-size: 0.55rem;
    color: rgba(255, 255, 255, 0.16);
    z-index: 50;
    text-align: right;
    pointer-events: none;
">
    <span>DAWSCAPE SYSTEM // DAW</span>
    <span style="margin-inline: 4px;">·</span>
    <a href="{{ route('legal') }}" style="color: inherit; text-decoration: none; pointer-events: auto;">
        AVISO LEGAL Y RGPD
    </a>
</div>
</body>
</html>
