<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>JUEGO DE PISTAS | DAWSCAPE</title>
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

  .team-info { font-size: 0.75rem; color: #b0e0bf; line-height: 1.6; text-shadow: 0 0 8px rgba(0, 255, 136, 0.2); }
  .team-info strong { color: var(--green); text-shadow: 0 0 10px var(--green); }

  .header-timer {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.3rem;
    color: var(--green);
    text-shadow: 0 0 10px var(--green);
    letter-spacing: 0.1em;
  }

  .screen { display: none; min-height: 100vh; }
  .screen.active {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    padding-top: 5rem;
    padding-bottom: 2rem;
    gap: 1rem;
  }

  .logo {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(1.8rem, 5vw, 3.5rem);
    font-weight: 900;
    color: var(--green);
    text-shadow: 0 0 20px var(--green), 0 0 60px #00ff8840;
    letter-spacing: 0.15em;
  }

  .subtitle {
    font-size: 0.8rem;
    color: #00cc66;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    text-shadow: 0 0 6px rgba(0, 204, 102, 0.2);
    text-align: center;
  }

  .panel {
    width: 100%;
    max-width: 720px;
    border: 1px solid var(--border);
    background: var(--panel);
    padding: 1.4rem 1.6rem;
    position: relative;
    box-shadow: 0 0 30px rgba(0, 255, 136, 0.08);
  }

  .panel::before {
    content: attr(data-label);
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.65rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
  }

  .question {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #aaffcc;
    white-space: pre-line;
  }

  .letters-row {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 0.8rem;
  }

  .letter-chip {
    border: 1px solid #00ff8840;
    background: rgba(0, 0, 0, 0.35);
    padding: 6px 10px;
    min-width: 34px;
    text-align: center;
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    color: var(--green);
    box-shadow: 0 0 12px rgba(0,255,136,0.08);
  }

  .answer-input {
    width: 100%;
    background: transparent;
    border: none;
    border-bottom: 2px solid var(--green);
    color: var(--green);
    font-family: 'Share Tech Mono', monospace;
    font-size: max(16px, 1.25rem);
    padding: 0.85rem 0.5rem;
    outline: none;
    letter-spacing: 0.08em;
    text-align: center;
    margin-top: 1.1rem;
  }

  .answer-input:focus { box-shadow: 0 2px 10px #00ff8820; }

  .btn {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.85rem;
    letter-spacing: 0.18em;
    padding: 1rem 1.2rem;
    border: 2px solid var(--green);
    background: transparent;
    color: var(--green);
    cursor: pointer;
    text-transform: uppercase;
    transition: all 0.25s;
    position: relative;
    overflow: hidden;
    width: 100%;
    margin-top: 1rem;
    min-height: 54px;
    -webkit-tap-highlight-color: transparent;
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
  .btn:hover { color: var(--bg); box-shadow: 0 0 18px rgba(0,255,136,0.25); }
  .btn:active { background: var(--green); color: var(--bg); }
  .btn:disabled { color: #33aa66; border-color: rgba(51,170,102,0.4); cursor: not-allowed; pointer-events: none; }

  .feedback {
    text-align: center;
    font-size: 0.85rem;
    min-height: 1.4rem;
    margin-top: 0.8rem;
    letter-spacing: 0.06em;
  }
  .feedback.ok { color: var(--green); }
  .feedback.err { color: var(--red); }

  .system-hint {
    border: 1px solid #ffaa0040;
    background: #110a00;
    padding: 0.9rem 1.1rem;
    color: var(--amber);
    font-size: 0.82rem;
    line-height: 1.7;
    display: none;
    margin-top: 1rem;
  }
  .system-hint.visible { display: block; }

  .terminal-ok {
    border: 1px solid var(--border);
    background: rgba(0, 0, 0, 0.35);
    padding: 1.2rem 1.4rem;
    max-width: 720px;
    width: 100%;
    line-height: 2.0;
    color: #88ccaa;
  }
  .terminal-ok .ok { color: var(--green); }

  @keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

  /* Matrix rain canvas */
  #matrix-canvas {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    z-index: 0;
    pointer-events: none;
    opacity: 0.12;
  }

  .screen { z-index: 1; position: relative; }
  .game-header { z-index: 100; }

  .matrix-intro {
    border: 1px solid var(--green);
    background: rgba(0, 0, 0, 0.85);
    padding: 1.4rem 1.6rem;
    max-width: 720px;
    width: 100%;
    font-size: 0.95rem;
    line-height: 2;
    color: var(--green);
    text-shadow: 0 0 8px var(--green);
    letter-spacing: 0.04em;
    box-shadow: 0 0 40px rgba(0, 255, 136, 0.15), inset 0 0 60px rgba(0, 255, 136, 0.03);
    animation: fadeIn 0.8s ease;
  }

  .matrix-intro .cursor {
    display: inline-block;
    width: 8px;
    height: 1.1em;
    background: var(--green);
    margin-left: 4px;
    animation: blink 0.8s step-end infinite;
    vertical-align: text-bottom;
  }

  @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }

  .typewriter {
    overflow: hidden;
    white-space: pre-wrap;
  }

  /* ── MOBILE RESPONSIVE ── */

  /* Fix: prevent content clipping when keyboard opens or content overflows */
  @supports (min-height: 100dvh) {
    .screen { min-height: 100dvh; }
  }

  @media (max-width: 600px) {
    .game-header {
      padding: 0.5rem 0.8rem;
    }

    .team-info { font-size: 0.65rem; line-height: 1.4; }

    .header-timer { font-size: 1rem; }

    .screen.active {
      padding: 1rem 0.8rem;
      padding-top: 4rem;
      padding-bottom: 1.5rem;
      justify-content: flex-start;
      gap: 0.8rem;
    }

    .logo {
      letter-spacing: 0.08em;
    }

    .subtitle {
      font-size: 0.68rem;
      letter-spacing: 0.12em;
    }

    .panel {
      padding: 1rem 0.9rem;
    }

    .panel::before {
      font-size: 0.55rem;
      top: -0.6rem;
    }

    .question {
      font-size: 0.92rem;
      line-height: 1.7;
    }

    .matrix-intro {
      padding: 1rem 1rem;
      font-size: 0.82rem;
      line-height: 1.8;
    }

    .btn {
      font-size: 0.72rem;
      letter-spacing: 0.1em;
      padding: 0.85rem 0.8rem;
      min-height: 48px;
    }

    .answer-input {
      font-size: 16px;
      padding: 0.7rem 0.4rem;
      margin-top: 0.8rem;
    }

    .letter-chip {
      padding: 5px 8px;
      min-width: 30px;
      font-size: 0.85rem;
    }

    .letters-row {
      gap: 6px;
    }

    .feedback { font-size: 0.78rem; }

    .system-hint {
      padding: 0.7rem 0.8rem;
      font-size: 0.75rem;
    }

    .terminal-ok {
      padding: 1rem 1rem;
      font-size: 0.85rem;
      line-height: 1.8;
    }
  }

  @media (max-width: 360px) {
    .logo {
      letter-spacing: 0.04em;
    }

    .panel {
      padding: 0.8rem 0.7rem;
    }

    .matrix-intro {
      padding: 0.8rem 0.7rem;
      font-size: 0.78rem;
    }

    .btn {
      font-size: 0.65rem;
      letter-spacing: 0.06em;
      padding: 0.75rem 0.6rem;
    }

    .letter-chip {
      padding: 4px 6px;
      min-width: 26px;
      font-size: 0.78rem;
    }
  }
</style>
</head>
<body>

@include('components.game-audio-setup', ['trackKey' => 'prueba9'])

<div class="game-header">
  <div class="team-info">
    <strong>Equipo:</strong> {{ session('nombre_equipo', 'Sin equipo') }}<br>
    <strong>Clase:</strong> {{ session('clase', 'N/A') }}
  </div>
  <div class="header-timer" id="cronometro">--:--</div>
</div>

<!-- MATRIX RAIN -->
<canvas id="matrix-canvas"></canvas>

<!-- INTRO -->
<div class="screen active" id="screen-intro">
  <div class="logo">JUEGO DE PISTAS</div>
  <div class="subtitle">// 5 preguntas → 5 letras → contraseña final</div>
  <div class="matrix-intro">
    <div class="typewriter" id="matrix-text"></div><span class="cursor"></span>
  </div>
  <button class="btn" id="btn-start" type="button" style="display:none;">▶ INICIAR PRUEBA</button>
</div>

<!-- QUESTION -->
<div class="screen" id="screen-question">
  <div class="subtitle" id="q-counter">PREGUNTA 01 / 05</div>
  <div class="panel" id="q-panel" data-label="// PREGUNTA">
    <div class="question" id="q-text">—</div>

    <input class="answer-input" id="answer-input" type="text" autocomplete="off" spellcheck="false" autocapitalize="off" placeholder="ESCRIBE LA RESPUESTA">
    <button class="btn" id="btn-send" type="button">ENVIAR RESPUESTA →</button>
    <div class="feedback" id="feedback"></div>
    <div class="system-hint" id="system-hint">[PISTA DEL SISTEMA] — Piensa en lo que ves cada día en el instituto.</div>
  </div>

  <div class="panel" data-label="// LETRAS OBTENIDAS">
    <div class="letters-row" id="letters-row"></div>
  </div>
</div>

<!-- PASSWORD -->
<div class="screen" id="screen-password">
  <div class="subtitle">// CONTRASEÑA FINAL</div>
  <div class="panel" data-label="// LETRAS OBTENIDAS">
    <div class="letters-row" id="letters-row-final"></div>
  </div>
  <div class="panel" data-label="// DESBLOQUEO DEL SISTEMA">
    <div class="question" style="text-align:center; color:var(--green); text-shadow: 0 0 8px var(--green);">
      Has conseguido todas las letras. La contraseña del sistema es: <strong>NACHO</strong>.<br>Introdúcela para desbloquear la salida.
    </div>
    <input class="answer-input" id="password-input" type="text" autocomplete="off" spellcheck="false" autocapitalize="off" placeholder="CONTRASEÑA">
    <button class="btn" id="btn-password" type="button">VERIFICAR CONTRASEÑA →</button>
    <div class="feedback" id="feedback-pass"></div>
    <div class="system-hint" id="system-hint-pass">[PISTA DEL SISTEMA] — Piensa en lo que ves cada día en el instituto.</div>
  </div>
</div>

<!-- DONE -->
<div class="screen" id="screen-done">
  <div class="subtitle">// SISTEMA CENTRAL</div>
  <div class="terminal-ok" id="terminal-ok">
    <span class="ok">[OK]</span> Verificando contraseña... 100%<br>
    <span class="ok">[OK]</span> Acceso concedido. Sistema desbloqueado.<br>
    <span class="ok">[OK]</span> ¡Has escapado de la clase de DAW!<br><br>
    "Contraseña NACHO aceptada. La puerta se ha desbloqueado.<br>
    ¡Enhorabuena, habéis completado el DAWSCAPE!"
  </div>
  <button class="btn" id="btn-final" type="button">VER RESULTADOS FINALES →</button>
</div>

<script>
  const QUESTIONS = [
    { q: "El director del centro se llama E_____o. Su nombre tiene 7 letras y es muy común en España. ¿Cómo se llama?", letter: "N" },
    { q: "Contad las ventanas de la clase de DAW. El número está entre 4 y 6.", letter: "A" },
    { q: "Vuestro centro tiene planta baja, primera y segunda planta. ¿Cuántas plantas tiene en total?", letter: "C" },
    { q: "En todo el centro, ¿cuántas clases están dedicadas exclusivamente a DAW (Desarrollo de Aplicaciones Web)?", letter: "H" },
    { q: "¿Qué marca coreana famosa por los móviles Galaxy y las TV QLED invierte en tu FP a través del programa XTART?", letter: "O" },
  ];

  const SERVER_PROGRESS = @json($progress ?? ['index' => 0, 'letters' => [], 'fails' => 0]);

  // TIMER
  let tiempoInicial = parseInt("{{ $tiempo_transcurrido ?? 0 }}") || 0;
  const loadTimeMs = Date.now();
  const timerDisplay = document.getElementById('cronometro');
  const MAX_MINUTOS = 90;
  function actualizarTimer() {
    const tiempo = tiempoInicial + Math.floor((Date.now() - loadTimeMs) / 1000);
    const m = Math.floor(tiempo / 60);
    const s = tiempo % 60;
    if (timerDisplay) timerDisplay.textContent = (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s);
    if (m >= MAX_MINUTOS) { window.location.href = '/final'; }
  }
  actualizarTimer();
  setInterval(actualizarTimer, 1000);

  // UI helpers
  function showScreen(id) {
    document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
  }

  const TOTAL_Q = QUESTIONS.length;

  function renderLetters(elId, letters) {
    const row = document.getElementById(elId);
    row.innerHTML = '';
    for (let i = 0; i < TOTAL_Q; i++) {
      const chip = document.createElement('div');
      chip.className = 'letter-chip';
      chip.textContent = letters[i] ? letters[i] : '_';
      row.appendChild(chip);
    }
  }

  let index = parseInt(SERVER_PROGRESS.index || 0);
  let letters = Array.isArray(SERVER_PROGRESS.letters) ? SERVER_PROGRESS.letters.slice(0) : [];
  let fails = parseInt(SERVER_PROGRESS.fails || 0);

  function refreshQuestionUI() {
    const counter = document.getElementById('q-counter');
    const qText = document.getElementById('q-text');
    const n = Math.min(index, QUESTIONS.length - 1);
    counter.textContent = `PREGUNTA ${String(n+1).padStart(2,'0')} / ${String(TOTAL_Q).padStart(2,'0')}`;
    qText.textContent = QUESTIONS[n].q;
    renderLetters('letters-row', letters);
    document.getElementById('feedback').textContent = '';
    document.getElementById('feedback').className = 'feedback';
    document.getElementById('system-hint').classList.toggle('visible', fails >= 5);
    const inp = document.getElementById('answer-input');
    inp.value = '';
    setTimeout(() => inp.focus(), 50);
  }

  async function postJSON(payload) {
    const res = await fetch('/prueba9', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(payload)
    });
    return await res.json();
  }

  let sendingAnswer = false;
  async function sendAnswer() {
    if (sendingAnswer) return;
    sendingAnswer = true;
    const btn = document.getElementById('btn-send');
    const inp = document.getElementById('answer-input');
    const feedback = document.getElementById('feedback');
    btn.disabled = true;
    const answer = (inp.value || '').trim();

    try {
      const data = await postJSON({ action: 'answer', answer });
      if (!data.success) {
        fails = data.fails || fails;
        feedback.className = 'feedback err';
        feedback.textContent = data.message || '[ERROR] Respuesta incorrecta. Inténtalo de nuevo.';
        document.getElementById('system-hint').classList.toggle('visible', fails >= 5);
        if (window.audioManager) window.audioManager.playSFX('error');
        btn.disabled = false;
        sendingAnswer = false;
        inp.focus();
        return;
      }

      // Correcta
      if (window.audioManager) window.audioManager.playSFX('correct');
      if (data.letter) letters.push(data.letter);
      renderLetters('letters-row', letters);

      // Si toca password screen
      if (data.state === 'password') {
        renderLetters('letters-row-final', data.letters || letters);
        showScreen('screen-password');
        document.getElementById('password-input').focus();
        sendingAnswer = false;
        return;
      }

      index = data.nextIndex || (index + 1);
      refreshQuestionUI();
      btn.disabled = false;
      sendingAnswer = false;
    } catch (e) {
      feedback.className = 'feedback err';
      feedback.textContent = '[ERROR] Fallo de conexión. Inténtalo de nuevo.';
      btn.disabled = false;
      sendingAnswer = false;
    }
  }

  let sendingPassword = false;
  async function sendPassword() {
    if (sendingPassword) return;
    sendingPassword = true;
    const btn = document.getElementById('btn-password');
    const inp = document.getElementById('password-input');
    const feedback = document.getElementById('feedback-pass');
    btn.disabled = true;

    try {
      const data = await postJSON({ action: 'password', password: inp.value || '' });
      if (!data.success) {
        fails = data.fails || fails;
        feedback.className = 'feedback err';
        feedback.textContent = data.message || '[ERROR] Contraseña incorrecta. Inténtalo de nuevo.';
        document.getElementById('system-hint-pass').classList.toggle('visible', fails >= 5);
        if (window.audioManager) window.audioManager.playSFX('error');
        btn.disabled = false;
        sendingPassword = false;
        inp.focus();
        return;
      }

      if (data.state === 'done') {
        if (window.audioManager) window.audioManager.playSFX('correct');
        showScreen('screen-done');
        sendingPassword = false;
        return;
      }
      btn.disabled = false;
      sendingPassword = false;
    } catch (e) {
      feedback.className = 'feedback err';
      feedback.textContent = '[ERROR] Fallo de conexión. Inténtalo de nuevo.';
      btn.disabled = false;
      sendingPassword = false;
    }
  }

  document.getElementById('btn-start').addEventListener('click', () => {
    if (index >= TOTAL_Q) {
      renderLetters('letters-row-final', letters);
      showScreen('screen-password');
      document.getElementById('password-input').focus();
    } else {
      showScreen('screen-question');
      refreshQuestionUI();
    }
  });
  document.getElementById('btn-send').addEventListener('click', sendAnswer);
  document.getElementById('btn-password').addEventListener('click', sendPassword);
  document.getElementById('btn-final').addEventListener('click', () => window.location.href = '/final');

  // Enter key support for inputs (guards prevent double-submit)
  document.getElementById('answer-input').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); sendAnswer(); } });
  document.getElementById('password-input').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); sendPassword(); } });

  // Matrix rain effect
  (function matrixRain() {
    const canvas = document.getElementById('matrix-canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    const cols = Math.floor(canvas.width / 18);
    const drops = Array(cols).fill(1);
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*';
    function draw() {
      ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = '#00ff88';
      ctx.font = '15px Share Tech Mono';
      for (let i = 0; i < drops.length; i++) {
        const ch = chars[Math.floor(Math.random() * chars.length)];
        ctx.fillText(ch, i * 18, drops[i] * 18);
        if (drops[i] * 18 > canvas.height && Math.random() > 0.975) drops[i] = 0;
        drops[i]++;
      }
    }
    setInterval(draw, 50);
    window.addEventListener('resize', () => { canvas.width = window.innerWidth; canvas.height = window.innerHeight; });
  })();

  // Typewriter effect for intro
  (function typewriter() {
    const text = 'Atenci\u00f3n, te has quedado atrapado en la clase de los alumnos de DAW. Tienes que resolver el acertijo para salir o nunca podr\u00e1s escapar. Cada respuesta correcta te dar\u00e1 una letra para la contrase\u00f1a final.';
    const el = document.getElementById('matrix-text');
    const btn = document.getElementById('btn-start');
    let i = 0;
    function type() {
      if (i < text.length) {
        el.textContent += text.charAt(i);
        i++;
        setTimeout(type, 28);
      } else {
        btn.style.display = '';
      }
    }
    type();
  })();

  // Si ya hay progreso en sesión, entrar directo a la pantalla correcta
  (function bootstrap() {
    if (index >= TOTAL_Q) {
      renderLetters('letters-row-final', letters);
      showScreen('screen-password');
    }
  })();
</script>
</body>
</html>

@include('components.live-listener')
