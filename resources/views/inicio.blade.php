<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>DAWSCAPE | Escape Room</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<!-- ICONOS -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');

  :root {
    --green: #00ff88;
    --green-rgb: 0, 255, 136;
    --red: #ff2244;
    --bg: #020c06;
    --panel: #041a0e;
    --border: rgba(0, 255, 136, 0.25);
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
    background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(var(--green-rgb),0.013) 2px, rgba(var(--green-rgb),0.013) 4px);
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

  /* MEJORADO: Subtitle con mejor contraste - cambio de #007744 a color más claro */
  .subtitle {
    font-size: 0.85rem;
    color: var(--green);
    letter-spacing: 0.3em;
    text-transform: uppercase;
    margin-bottom: 2rem;
    text-align: center;
    text-shadow: 0 0 10px rgba(var(--green-rgb), 0.2);
  }

  .glitch-text { animation: blink 2s infinite; color: var(--red); font-size: 0.9rem; text-shadow: 0 0 10px var(--red); letter-spacing: 0.1em; text-align: center; margin-bottom: 2rem; }
  @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

  .login-card {
    width: 100%;
    max-width: 500px;
    border: 1px solid var(--border);
    background: var(--panel);
    padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
    position: relative;
    box-shadow: 0 0 30px rgba(var(--green-rgb), 0.1);
  }

  .login-card::before {
    content: '// IDENTIFICACIÓN';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.7rem;
    letter-spacing: 0.2em;
  }

  .form-group { margin-bottom: 1.8rem; }

  /* MEJORADO: Form label con mejor contraste */
  .form-label {
    display: block;
    font-size: 0.75rem;
    color: var(--green);
    letter-spacing: 0.15em;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    text-shadow: 0 0 6px rgba(var(--green-rgb), 0.15);
  }

  .form-control {
    width: 100%;
    background: transparent;
    border: none;
    border-bottom: 2px solid var(--border);
    color: var(--green);
    font-family: 'Share Tech Mono', monospace;
    font-size: max(16px, 1.1rem); /* Prevents iOS zoom on focus */
    padding: 0.8rem 0.5rem;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
  }

  .form-control::placeholder { color: #88ccaa60; } /* Better WCAG contrast */
  .form-control:focus { border-bottom-color: var(--green); box-shadow: 0 4px 15px -10px var(--green); }

  /* MEJORADO: Helper text con mejor contraste */
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

  /* Customization Selectors */
  .grid-selector { display: grid; grid-template-columns: repeat(8, 1fr); gap: 0.5rem; margin-top: 0.5rem; }
  .color-btn {
    aspect-ratio: 1; border-radius: 50%; border: 2px solid var(--panel); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
  }
  .color-btn:hover { transform: scale(1.1); }
  .color-btn.active { border-color: var(--text); box-shadow: 0 0 15px currentColor; transform: scale(1.1); }

  .icon-btn {
    aspect-ratio: 1; border-radius: 8px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; cursor: pointer; transition: all 0.2s; color: var(--green); background: rgba(var(--green-rgb), 0.05);
  }
  .icon-btn:hover { background: rgba(var(--green-rgb), 0.2); }
  .icon-btn.active { background: var(--green); color: var(--bg); border-color: var(--green); box-shadow: 0 0 15px var(--green); }

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

  .error-feedback { color: var(--red); text-align: center; font-size: 0.85rem; margin-bottom: 1.5rem; letter-spacing: 0.08em; border: 1px dashed var(--red); padding: 0.8rem; background: rgba(255, 34, 68, 0.05); }

  @keyframes pulse-glow {
    0%, 100% { text-shadow: 0 0 20px var(--green), 0 0 50px rgba(var(--green-rgb), 0.38); }
    50% { text-shadow: 0 0 40px var(--green), 0 0 90px rgba(var(--green-rgb), 0.25); }
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
    .grid-selector { grid-template-columns: repeat(8, 1fr); gap: 0.3rem; }
    .icon-btn { font-size: 1.1rem; }
    .form-control { font-size: max(16px, 1rem); }
    .btn { font-size: 0.85rem; letter-spacing: 0.15em; }
  }

  /* Dynamic theme classes */
  .info-banner {
    max-width: 500px; margin-bottom: 2rem; padding: 1.5rem;
    background: rgba(var(--green-rgb), 0.08);
    border: 1px solid rgba(var(--green-rgb), 0.3);
    border-radius: 0.5rem; color: #cbd5e1; text-align: center;
    font-size: 0.95rem; line-height: 1.6;
  }
  .info-banner-link {
    display: inline-block; padding: 0.8rem 1.5rem;
    background: rgba(var(--green-rgb), 0.2);
    border: 1px solid var(--green); color: var(--green);
    text-decoration: none; border-radius: 0.35rem;
    font-weight: 600; transition: all 0.3s;
    text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.1em;
  }
  .info-banner-link:hover {
    background: rgba(var(--green-rgb), 0.35);
    box-shadow: 0 0 15px rgba(var(--green-rgb), 0.3);
  }
  .instructions-box {
    background: rgba(241, 245, 249, 0.03);
    border-left: 3px solid var(--green);
    padding: 1rem; margin-bottom: 2rem; border-radius: 0.3rem;
    color: #cbd5e1; font-size: 0.9rem;
  }

  /* ====== CINEMATIC OVERLAY ====== */
  .cinematic-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: #000; display: none; flex-direction: column;
    align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.6s;
  }
  .cinematic-overlay.active { display: flex; opacity: 1; }

  .cine-scanlines {
    position: absolute; inset: 0; pointer-events: none;
    background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.015) 2px, rgba(255,255,255,0.015) 4px);
  }

  .cine-logo {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(3rem, 8vw, 6rem);
    font-weight: 900; color: var(--green);
    text-shadow: 0 0 40px var(--green), 0 0 120px rgba(var(--green-rgb), 0.5);
    letter-spacing: 0.25em; opacity: 0;
    transform: scale(0.5);
    animation: cine-logo-in 1.2s ease-out 0.5s forwards;
  }

  .cine-line {
    width: 0; height: 2px;
    background: var(--green);
    box-shadow: 0 0 20px var(--green);
    margin: 1.5rem 0;
    animation: cine-line-expand 0.8s ease-out 1.4s forwards;
  }

  .cine-text {
    font-family: 'Share Tech Mono', monospace;
    color: var(--green); font-size: clamp(0.85rem, 2.5vw, 1.2rem);
    letter-spacing: 0.15em; text-align: center;
    opacity: 0; max-width: 600px; padding: 0 1rem;
    line-height: 1.8;
  }
  .cine-text-1 { animation: cine-fade-in 0.8s ease-out 2s forwards; }
  .cine-text-2 { animation: cine-fade-in 0.8s ease-out 3.2s forwards; font-size: clamp(0.75rem, 2vw, 0.95rem); color: rgba(var(--green-rgb), 0.6); margin-top: 0.5rem; }

  .cine-status {
    font-family: 'Share Tech Mono', monospace;
    font-size: 0.7rem; color: rgba(var(--green-rgb), 0.4);
    letter-spacing: 0.3em; text-transform: uppercase;
    position: absolute; bottom: 2rem;
    opacity: 0; animation: cine-fade-in 0.5s ease-out 4.2s forwards;
  }
  .cine-status::after {
    content: ''; display: inline-block;
    width: 6px; height: 6px; background: var(--green);
    border-radius: 50%; margin-left: 8px;
    animation: cine-blink 1s infinite 4.2s;
  }

  @keyframes cine-logo-in {
    to { opacity: 1; transform: scale(1); }
  }
  @keyframes cine-line-expand {
    to { width: min(350px, 70vw); }
  }
  @keyframes cine-fade-in {
    to { opacity: 1; }
  }
  @keyframes cine-blink {
    0%, 100% { opacity: 1; } 50% { opacity: 0.2; }
  }
</style>
</head>
<body>

@include('components.game-audio-setup', ['trackKey' => 'login'])

<div class="particles" id="particles"></div>

<div class="screen">
  <div class="logo">DAWSCAPE</div>
  <div class="glitch-text">ACCESO RESTRINGIDO. IDENTIFÍQUESE.</div>

  <!-- BANNER INFORMATIVO -->
  <div class="info-banner">
    <p style="margin-bottom: 1rem;"><strong style="color: var(--green);">¿PRIMERA VEZ?</strong></p>
    <p style="margin-bottom: 1.5rem;">Si aún no te has inscrito, debes hacerlo primero. La inscripción te permite registrar tu equipo y recibir por email las credenciales necesarias para acceder al juego.</p>
    <a href="/inscripcion" class="info-banner-link menu-click">→ Ir a Inscripción</a>
  </div>

  <div class="login-card">
    <form action="/empezar" method="POST">
      @csrf
      {{-- Preserva el destino cuando se llega desde un QR sin sesión --}}
      <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">

      @if(session('error'))
        <div class="error-feedback">✖ {{ session('error') }}</div>
      @endif

      <div class="instructions-box">
        <strong style="color: var(--green);">INSTRUCCIONES:</strong> Utiliza los datos que recibiste en tu email de inscripción.
      </div>

      <div class="form-group">
        <label class="form-label">CORREO INSTITUCIONAL</label>
        <input type="email" name="correo" class="form-control" placeholder="usuario@estudiante.xtart.com" required autocomplete="off" autofocus>
        <span class="helper-text">* Solo se permite el dominio @estudiante.xtart.com</span>
      </div>

      <input type="hidden" name="icono" id="icono_input" value="ph-first-aid">
      <input type="hidden" name="color_hex" id="color_input" value="#00cc44">

      <!-- SELECCIÓN DE AVATAR -->
      <div class="form-group" style="margin-top: 1.5rem;">
        <label class="form-label">SELECCIONA TU AVATAR</label>
        <div class="grid-selector" id="icon-selector">
          <div class="icon-btn active" data-val="ph-first-aid" title="Primeros Auxilios (Sanidad)"><i class="ph ph-first-aid"></i></div>
          <div class="icon-btn" data-val="ph-heartbeat" title="Cardiología (Sanidad)"><i class="ph ph-heartbeat"></i></div>
          <div class="icon-btn" data-val="ph-tooth" title="Odontólogo (Dentistas)"><i class="ph ph-tooth"></i></div>
          <div class="icon-btn" data-val="ph-pill" title="Farmacia (Parafarmacia)"><i class="ph ph-pill"></i></div>
          <div class="icon-btn" data-val="ph-fire-extinguisher" title="Extintor (Emergencias)"><i class="ph ph-fire-extinguisher"></i></div>
          <div class="icon-btn" data-val="ph-siren" title="Ambulancia (Protección Civil)"><i class="ph ph-siren"></i></div>
          <div class="icon-btn" data-val="ph-megaphone" title="Megáfono (Marketing)"><i class="ph ph-megaphone"></i></div>
          <div class="icon-btn" data-val="ph-chart-line-up" title="Crecimiento (Marketing)"><i class="ph ph-chart-line-up"></i></div>
        </div>
      </div>

      <!-- SELECCIÓN DE COLOR NEÓN -->
      <div class="form-group" style="margin-top: 1rem; margin-bottom: 2.5rem;">
        <label class="form-label">IDENTIFICADOR DE COLOR</label>
        <div class="grid-selector" id="color-selector">
          <div class="color-btn active" data-val="#00cc44" style="background:#00cc44;" title="Verde"></div>
          <div class="color-btn" data-val="#ff69b4" style="background:#ff69b4;" title="Rosa"></div>
          <div class="color-btn" data-val="#8b00ff" style="background:#8b00ff;" title="Morado"></div>
          <div class="color-btn" data-val="#ff6600" style="background:#ff6600;" title="Naranja"></div>
          <div class="color-btn" data-val="#0066ff" style="background:#0066ff;" title="Azul"></div>
          <div class="color-btn" data-val="#ffdd00" style="background:#ffdd00;" title="Amarillo"></div>
          <div class="color-btn" data-val="#ff1a1a" style="background:#ff1a1a;" title="Rojo"></div>
          <div class="color-btn" data-val="#ffffff" style="background:#ffffff;" title="Blanco"></div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">CÓDIGO DE ACCESO</label>
        <input type="password" name="codigo_acceso" class="form-control" placeholder="6 caracteres" required autocomplete="off" maxlength="6">
        <span class="helper-text">Lo recibiste en tu correo de inscripción</span>
      </div>

      <div class="checkbox-group">
        <input type="checkbox" id="checkHonestidad" required>
        <label class="checkbox-label" for="checkHonestidad">
            Certifico bajo honor que estos son mis datos reales de inscripción.
        </label>
      </div>

      <button type="button" class="btn" id="btnIniciar">INICIAR CONEXIÓN →</button>
    </form>
  </div>
</div>

<script>
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

// Minecraft click sound — usa Web Audio API vía audioManager para latencia mínima
function playClick() {
  if (window.audioManager) {
    window.audioManager.playSFX('click');
  }
}

// Hex → RGB
function hexToRgb(hex) {
  return [parseInt(hex.slice(1,3),16), parseInt(hex.slice(3,5),16), parseInt(hex.slice(5,7),16)];
}

// Dynamic theme color
function setThemeColor(hex) {
  const [r, g, b] = hexToRgb(hex);
  const s = document.documentElement.style;
  s.setProperty('--green', hex);
  s.setProperty('--green-rgb', `${r}, ${g}, ${b}`);
  s.setProperty('--border', `rgba(${r}, ${g}, ${b}, 0.25)`);
  document.querySelectorAll('.particle').forEach(p => p.style.background = hex);
}

// Icon selector
const iconBtns = document.querySelectorAll('#icon-selector .icon-btn');
const iconInput = document.getElementById('icono_input');
iconBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    playClick();
    iconBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    iconInput.value = btn.getAttribute('data-val');
  });
});

// Color selector
const colorBtns = document.querySelectorAll('#color-selector .color-btn');
const colorInput = document.getElementById('color_input');
colorBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    playClick();
    colorBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const hex = btn.getAttribute('data-val');
    colorInput.value = hex;
    setThemeColor(hex);
  });
});

// Apply initial theme from default selected color
setThemeColor(colorInput.value);

// Click sound on all menu buttons & links
document.querySelectorAll('.btn, .menu-click, .checkbox-label').forEach(el => {
  el.addEventListener('click', playClick);
});

// ====== CINEMATIC on form submit ======
const form = document.querySelector('form');
const btnIniciar = document.getElementById('btnIniciar');

btnIniciar.addEventListener('click', function() {
  playClick();
  // Validate the form natively
  if (!form.reportValidity()) return;

  // Show cinematic (element is below this script in the DOM, so query it here)
  const cinematic = document.getElementById('cinematic');
  if (cinematic) {
    cinematic.style.display = 'flex';
    requestAnimationFrame(() => cinematic.classList.add('active'));
  }

  // Submit after cinematic plays (5s)
  setTimeout(() => {
    form.submit();
  }, 5000);
});
</script>
<!-- CINEMATIC OVERLAY -->
<div class="cinematic-overlay" id="cinematic">
  <div class="cine-scanlines"></div>
  <div class="cine-logo">DAWSCAPE</div>
  <div class="cine-line"></div>
  <div class="cine-text cine-text-1">BIENVENIDO, AGENTE.</div>
  <div class="cine-text cine-text-2">Estás a punto de entrar en el mejor escape room de XTART.<br>Resuelve los enigmas. Demuestra tu ingenio. Escapa.</div>
  <div class="cine-status">ESTABLECIENDO CONEXIÓN SEGURA</div>
</div>

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
