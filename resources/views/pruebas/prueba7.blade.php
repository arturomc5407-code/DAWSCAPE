<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>EL CÓDICE OCULTO | DAWSCAPE</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');

  :root {
    --green: #00ff88;
    --red: #ff2244;
    --amber: #ffaa00;
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

  /* HEADER */
  .game-header {
    position: fixed;
    top: 0; left: 0; right: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 1.5rem;
    background: rgba(2, 12, 6, 0.95);
    border-bottom: 1px solid var(--border);
    z-index: 100;
    backdrop-filter: blur(8px);
  }

  .team-info { font-size: 0.75rem; color: #88ccaa; line-height: 1.6; }
  .team-info strong { color: var(--green); }

  .header-timer {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.3rem;
    color: var(--green);
    text-shadow: 0 0 10px var(--green);
    letter-spacing: 0.1em;
  }

  /* SCREENS */
  .screen { display: none; min-height: 100vh; }
  .screen.active {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    padding-top: 5rem;
    padding-bottom: 2rem;
  }

  /* INTRO */
  #screen-intro { text-align: center; gap: 2rem; animation: fadeIn 1s ease; }

  .logo {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(1.8rem, 5vw, 3.5rem);
    font-weight: 900;
    color: var(--green);
    text-shadow: 0 0 20px var(--green), 0 0 60px #00ff8840;
    letter-spacing: 0.15em;
    animation: pulse-glow 2.5s ease-in-out infinite;
  }

  .subtitle {
    font-size: 0.85rem;
    color: #00cc66;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    text-shadow: 0 0 6px rgba(0, 204, 102, 0.2);
  }

  .intro-text {
    max-width: 620px;
    line-height: 1.9;
    color: #88ccaa;
    border: 1px solid var(--border);
    padding: clamp(1.2rem, 4vw, 1.8rem) clamp(1.2rem, 4vw, 2rem);
    background: var(--panel);
    position: relative;
    font-size: clamp(0.93rem, 3.5vw, 1.02rem);
    text-align: left;
  }

  .intro-text::before {
    content: '// MISIÓN CRÍTICA';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.7rem;
    letter-spacing: 0.2em;
  }

  /* BUTTONS */
  .btn {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.9rem;
    letter-spacing: 0.2em;
    padding: 1rem 2.5rem;
    border: 2px solid var(--green);
    background: transparent;
    color: var(--green);
    cursor: pointer;
    text-transform: uppercase;
    transition: all 0.25s;
    position: relative;
    overflow: hidden;
    width: 100%;
    max-width: 400px;
    text-align: center;
    min-height: 52px;
    -webkit-tap-highlight-color: transparent;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
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
  .btn:hover { color: var(--bg); }
  .btn:active { background: var(--green); color: var(--bg); }
  .btn:disabled { color: #33aa66; border-color: rgba(51,170,102,0.4); cursor: not-allowed; pointer-events: none; }

  /* ─── LOCK SCREEN ─── */
  #screen-lock {
    gap: 1.5rem;
    max-width: 680px;
    width: 100%;
    animation: fadeIn 0.5s ease;
  }

  .lock-header {
    width: 100%;
    border-bottom: 1px solid var(--border);
    padding-bottom: 0.8rem;
    text-align: center;
  }

  .lock-title {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(1.2rem, 4vw, 1.8rem);
    color: var(--green);
    letter-spacing: 0.2em;
    margin-bottom: 0.3rem;
  }

  .lock-subtitle { font-size: 0.75rem; color: #00cc66; letter-spacing: 0.2em; text-shadow: 0 0 6px rgba(0, 204, 102, 0.2); }

  .lock-panel {
    width: 100%;
    border: 1px solid var(--border);
    background: var(--panel);
    padding: clamp(1.5rem, 4vw, 2rem);
    position: relative;
  }

  .lock-panel::before {
    content: '// CERRADURA DIGITAL — CÓDIGO MAESTRO';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.65rem;
    letter-spacing: 0.2em;
  }

  /* The 4-digit lock */
  .digits-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  /* Responsive 2x2 grid for mobile */
  @media (max-width: 768px) {
    .digits-row {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  .digit-slot { display: flex; flex-direction: column; align-items: center; gap: 0.6rem; }

  .digit-label {
    font-size: 0.6rem;
    color: #00cc66;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    text-shadow: 0 0 6px rgba(0, 204, 102, 0.2);
  }

  .digit-input {
    width: 100%;
    max-width: 80px;
    aspect-ratio: 1;
    background: #011208;
    border: 2px solid var(--green);
    color: var(--green);
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(1.5rem, 5vw, 2.2rem);
    font-weight: 700;
    text-align: center;
    outline: none;
    letter-spacing: 0;
    transition: border-color 0.2s, box-shadow 0.2s;
    appearance: textfield;
    -moz-appearance: textfield;
    caret-color: var(--green);
  }

  .digit-input::-webkit-inner-spin-button,
  .digit-input::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }

  .digit-input:focus { box-shadow: 0 0 12px #00ff8840; }
  .digit-input.wrong { border-color: var(--red); color: var(--red); animation: shake 0.4s ease; }
  .digit-input.correct { border-color: var(--green); background: #00ff8812; }

  .hint-btn {
    font-family: 'Share Tech Mono', monospace;
    font-size: 0.65rem;
    color: #00cc66;
    background: transparent;
    border: 1px solid #00ff8830;
    padding: 0.4rem 0.6rem;
    cursor: pointer;
    letter-spacing: 0.1em;
    transition: all 0.2s;
    text-transform: uppercase;
    white-space: nowrap;
    -webkit-tap-highlight-color: transparent;
    min-height: 44px;
  }

  .hint-btn:hover, .hint-btn:focus { border-color: var(--green); color: var(--green); background: #00ff8808; }

  /* Modal de pista */
  .hint-modal {
    position: fixed;
    inset: 0;
    background: rgba(2,12,6,0.92);
    z-index: 500;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
  }

  .hint-modal.open { display: flex; animation: fadeIn 0.2s ease; }

  .hint-box {
    border: 1px solid var(--green);
    background: var(--panel);
    padding: 2rem;
    max-width: 480px;
    width: 100%;
    position: relative;
  }

  .hint-box::before {
    content: '// PISTA DE UBICACIÓN';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--panel);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.65rem;
    letter-spacing: 0.2em;
  }

  .hint-close {
    position: absolute;
    top: 0.8rem; right: 0.8rem;
    background: transparent;
    border: none;
    color: var(--green);
    font-size: 1.2rem;
    cursor: pointer;
  }

  .hint-slot-label {
    color: #00cc66;
    font-size: 0.7rem;
    letter-spacing: 0.2em;
    margin-bottom: 0.6rem;
    text-transform: uppercase;
    text-shadow: 0 0 6px rgba(0, 204, 102, 0.2);
  }

  .hint-location {
    font-size: 1.1rem;
    color: var(--green);
    line-height: 1.6;
    margin-bottom: 1rem;
  }

  .hint-note {
    font-size: 0.8rem;
    color: #446655;
    border-top: 1px solid #00ff8820;
    padding-top: 0.8rem;
  }

  /* Feedback & submit */
  .feedback-line {
    text-align: center;
    font-size: 0.85rem;
    min-height: 1.4rem;
    letter-spacing: 0.08em;
    margin-top: 0.5rem;
  }

  .feedback-line.ok { color: var(--green); }
  .feedback-line.err { color: var(--red); }

  .system-hint {
    border: 1px solid #ffaa0040;
    background: #110a00;
    padding: 1rem 1.2rem;
    color: var(--amber);
    font-size: 0.82rem;
    line-height: 1.7;
    display: none;
  }

  .system-hint.visible { display: block; animation: fadeIn 0.5s ease; }

  /* ─── WIN SCREEN ─── */
  #screen-win { text-align: center; gap: 1.5rem; animation: fadeIn 1s ease; }

  .win-icon { font-size: 4rem; filter: drop-shadow(0 0 20px var(--green)); }

  .win-title {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(1.8rem, 5vw, 3rem);
    font-weight: 900;
    color: var(--green);
    text-shadow: 0 0 30px var(--green);
    animation: pulse-glow 1.5s ease-in-out infinite;
  }

  .terminal-block {
    border: 1px solid var(--border);
    background: var(--panel);
    padding: 1.5rem 2rem;
    text-align: left;
    line-height: 2.2;
    font-size: 0.95rem;
    color: #88ccaa;
    max-width: 500px;
    width: 100%;
    position: relative;
  }

  .terminal-block::before {
    content: '// SISTEMA';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.65rem;
    letter-spacing: 0.2em;
  }

  .t-ok { color: var(--green); }
  .t-final { color: #aaffcc; font-size: 1rem; margin-top: 0.5rem; display: block; }

  /* PARTICLES */
  .particles { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
  .particle {
    position: absolute;
    width: 2px; height: 2px;
    background: var(--green);
    border-radius: 50%;
    opacity: 0;
    animation: float linear infinite;
  }

  /* ANIMATIONS */
  @keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes pulse-glow {
    0%, 100% { text-shadow: 0 0 20px var(--green), 0 0 50px #00ff8860; }
    50% { text-shadow: 0 0 40px var(--green), 0 0 90px #00ff8840; }
  }
  @keyframes shake {
    0%,100% { transform: translateX(0); }
    20% { transform: translateX(-8px); }
    40% { transform: translateX(8px); }
    60% { transform: translateX(-5px); }
    80% { transform: translateX(5px); }
  }
  @keyframes float {
    0% { opacity: 0; transform: translateY(100vh); }
    10% { opacity: 0.5; }
    90% { opacity: 0.3; }
    100% { opacity: 0; transform: translateY(-10vh); }
  }

  /* MOBILE */
  @media (max-width: 600px) {
    #screen-lock { max-width: 100%; }
    .screen.active { padding-left: 1rem; padding-right: 1rem; padding-top: 5.5rem; }
    .game-header { padding: 0.6rem 1rem; }
    .team-info { font-size: 0.7rem; }
    .header-timer { font-size: 1.1rem; }
    .btn { font-size: 0.8rem; letter-spacing: 0.1em; padding: 0.8rem 1rem; }
    .digits-row { gap: 0.6rem; }
    .digit-input { font-size: clamp(1.4rem, 9vw, 2rem); }
    .intro-text { font-size: 0.9rem; }
  }
</style>
</head>
<body>
  @include('components.game-audio-setup', ['trackKey' => 'prueba7'])
<!-- HEADER -->
<div class="game-header">
  <div class="team-info">
    <strong>Equipo:</strong> {{ session('nombre_equipo', 'Sin equipo') }}<br>
    <strong>Clase:</strong> {{ session('clase', 'N/A') }}
  </div>
  <div class="header-timer" id="cronometro">--:--</div>
</div>

<div class="particles" id="particles"></div>

<!-- MODAL PISTA DE UBICACIÓN -->
<div class="hint-modal" id="hintModal" onclick="if(event.target===this)closeHint()">
  <div class="hint-box">
    <button class="hint-close" onclick="closeHint()" aria-label="Cerrar">✕</button>
    <div class="hint-slot-label" id="hintSlotLabel">Dígito 1</div>
    <div class="hint-location" id="hintLocation">—</div>
  </div>
</div>

<!-- INTRO -->
<div class="screen active" id="screen-intro">
  <div class="logo">🔒 CÓDICE OCULTO</div>
  <div class="subtitle">// Protocolo de acceso maestro</div>
  <div class="intro-text">
    <p>Frente a vosotros tenéis una <strong style="color:var(--green)">cerradura digital</strong> protegida por un código maestro de cuatro dígitos. No intentéis adivinarlo al azar; el sistema de seguridad es dinámico y los cuatro números cambian en cada partida.</p>
    <br>
    <p>Para descubrir la combinación exacta, tendréis que <strong style="color:var(--green)">combinar vuestro ingenio digital con la exploración en el mundo real.</strong></p>
    <br>
    <p style="color:#445544">Pulsad sobre cada posición del candado para ver la pista de ubicación. Buscad el QR, escaneadlo y resolved el acertijo.</p>
  </div>
  <button class="btn" onclick="startLock()">▶ ACCEDER AL CANDADO</button>
</div>

<!-- LOCK (CANDADO) -->
<div class="screen" id="screen-lock">
  <div class="lock-header">
    <div class="lock-title">🔐 CÓDIGO MAESTRO</div>
    <div class="lock-subtitle">// Introduce los 4 dígitos en el orden correcto</div>
  </div>

  <div class="lock-panel">
    <div class="digits-row">
      @foreach($pistas as $index => $ubicacion)
      <div class="digit-slot">
        <div class="digit-label">Dígito {{ $index + 1 }}</div>
        <input
          class="digit-input"
          id="digit-{{ $index }}"
          type="tel"
          inputmode="numeric"
          maxlength="1"
          pattern="[0-9]"
          autocomplete="off"
          spellcheck="false"
          placeholder="?"
          data-index="{{ $index }}"
          oninput="handleDigitInput(this)"
          onkeydown="handleDigitKeydown(event, this)"
        >
        <div style="margin-top: 10px;">
          <button class="hint-btn" data-hint-index="{{ $index }}" onclick="showHint(parseInt(this.getAttribute('data-hint-index')))">📍 Ver Ubicación</button>
        </div>
      </div>
      @endforeach
    </div>

    <div class="system-hint{{ $intentos_fallidos >= 5 ? ' visible' : '' }}" id="system-hint">
      [PISTA DEL SISTEMA] — Aseguraos de introducir los dígitos en el orden correcto: primero el del dígito 1, luego el 2, el 3 y el 4.
    </div>

    <div class="feedback-line" id="feedback"></div>

    <br>
    <button id="check-btn" class="btn" onclick="checkCombination()" style="width:100%; max-width:100%">
      🔓 COMPROBAR CÓDIGO
    </button>
  </div>
</div>

<!-- WIN -->
<div class="screen" id="screen-win">
  <div class="win-icon">🔓</div>
  <div class="win-title">CANDADO ABIERTO</div>
  <div class="subtitle">// Códice Oculto descifrado</div>
  <div class="terminal-block" id="win-terminal">
    <span class="t-ok">[OK] Verificando secuencia... 100%</span><br>
    <span class="t-ok">[OK] Código maestro validado.</span><br>
    <span class="t-ok">[OK] Cerradura digital desbloqueada.</span>
    <span class="t-final">¡Habéis descifrado el Códice Oculto.<br>El sistema DAWSCAPE ha sido completamente restaurado.</span>
  </div>
  <a href="/prueba8" class="btn">CONTINUAR A PRUEBA 8 →</a>
</div>

<script>
// ── CONFIG INYECTADA DESDE EL SERVIDOR ─────────────────────
const PISTAS = @json($pistas);
let intentosFallidos = parseInt("{{ $intentos_fallidos ?? 0 }}") || 0;

// ── TIMER (global para compatibilidad con pista-ayuda) ──────
var tiempoInicial = parseInt("{{ $tiempo_transcurrido ?? 0 }}") || 0;
const loadTimeMs = Date.now();
const timerDisplay = document.getElementById('cronometro');
const MAX_MINUTOS = 90;

function actualizarTimer() {
  var tiempo = tiempoInicial + Math.floor((Date.now() - loadTimeMs) / 1000);
  var m = Math.floor(tiempo / 60);
  var s = tiempo % 60;
  if (timerDisplay) timerDisplay.textContent = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
  if (m >= MAX_MINUTOS) { window.location.href = '/final'; return; }
}

actualizarTimer();
setInterval(actualizarTimer, 1000);

// ── PARTÍCULAS ──────────────────────────────────────────────
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

// ── SCREENS ─────────────────────────────────────────────────
function showScreen(id) {
  document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
  document.getElementById(id).classList.add('active');
}

function startLock() {
  showScreen('screen-lock');
  document.getElementById('digit-0').focus();
}

// ── DIGIT INPUT HANDLING ────────────────────────────────────
function handleDigitInput(input) {
  const val = input.value.replace(/[^0-9]/g, '');
  input.value = val.length > 1 ? val.slice(-1) : val;

  const idx = parseInt(input.dataset.index);
  if (input.value.length === 1 && idx < 3) {
    document.getElementById('digit-' + (idx + 1)).focus();
  }
}

function handleDigitKeydown(e, input) {
  const idx = parseInt(input.dataset.index);
  if (e.key === 'Backspace' && input.value === '' && idx > 0) {
    e.preventDefault();
    const prev = document.getElementById('digit-' + (idx - 1));
    prev.value = '';
    prev.focus();
  }

  if (e.key === 'ArrowRight' && idx < 3) {
    e.preventDefault();
    document.getElementById('digit-' + (idx + 1)).focus();
  }
  if (e.key === 'ArrowLeft' && idx > 0) {
    e.preventDefault();
    document.getElementById('digit-' + (idx - 1)).focus();
  }
}

// ── HINT MODAL ───────────────────────────────────────────────
function showHint(slotIndex) {
  document.getElementById('hintSlotLabel').textContent = 'Dígito ' + (slotIndex + 1);
  document.getElementById('hintLocation').textContent = PISTAS[slotIndex] || '—';
  document.getElementById('hintModal').classList.add('open');
}

function closeHint() {
  document.getElementById('hintModal').classList.remove('open');
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeHint();
});

// ── COMBINATION CHECK ────────────────────────────────────────
let isTransitioning = false;

function checkCombination() {
  if (isTransitioning) return;

  const digits = [];
  const inputs = [];
  for (let i = 0; i < 4; i++) {
    const inp = document.getElementById('digit-' + i);
    inputs.push(inp);
    const v = inp.value.trim();
    if (v === '' || !/^[0-9]$/.test(v)) {
      setFeedback('[ERROR] Introduce todos los dígitos antes de verificar.', 'err');
      inp.classList.add('wrong');
      setTimeout(() => inp.classList.remove('wrong'), 1000);
      return;
    }
    digits.push(parseInt(v));
  }

  isTransitioning = true;
  const btn = document.getElementById('check-btn');
  btn.disabled = true;
  btn.textContent = '[ VERIFICANDO... ]';

  fetch('/prueba7', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ digits })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      if (window.audioManager) window.audioManager.playSFX('correct');
      inputs.forEach(inp => { inp.classList.remove('wrong'); inp.classList.add('correct'); });
      showScreen('screen-win');
    } else {
      if (window.audioManager) window.audioManager.playSFX('error');
      intentosFallidos = data.intentos || (intentosFallidos + 1);
      inputs.forEach(inp => { inp.value = ''; inp.classList.remove('correct'); inp.classList.add('wrong'); });
      setFeedback('[ERROR] Secuencia incorrecta. Revisad los enigmas.', 'err');

      if (intentosFallidos >= 5) {
        document.getElementById('system-hint').classList.add('visible');
      }

      setTimeout(() => {
        inputs.forEach(inp => inp.classList.remove('wrong'));
        btn.disabled = false;
        btn.textContent = '🔓 COMPROBAR CÓDIGO';
        isTransitioning = false;
        if (document.getElementById('digit-0')) {
          document.getElementById('digit-0').focus();
        }
      }, 1800);
    }
  })
  .catch(err => {
    console.error(err);
    setFeedback('[ERROR] Fallo de conexión. Inténtalo de nuevo.', 'err');
    btn.disabled = false;
    btn.textContent = '🔓 COMPROBAR CÓDIGO';
    isTransitioning = false;
  });
}

function setFeedback(msg, cls) {
  const el = document.getElementById('feedback');
  el.textContent = msg;
  el.className = 'feedback-line ' + cls;
}
</script>
</body>
</html>

@include('components.pista-ayuda', ['pruebaNumero' => 7, 'pistaConfig' => $pistaConfig, 'yaUsada' => $yaUsada])

@include('components.live-listener')

