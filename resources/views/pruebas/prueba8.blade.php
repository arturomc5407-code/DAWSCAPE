<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>CORTAFUEGOS | DAWSCAPE</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<meta name="quiz-data" content="{{ $preguntasAsignadas->map(function($pregunta, $index) {
    return [
        'label' => '// PROTOCOLO ' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
        'riddle' => $pregunta->enunciado,
        'answer' => $pregunta->respuesta_correcta,
        'alternatives' => $pregunta->respuestas_alternativas ?? []
    ];
})->toJson() }}">
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

  /* MEJORADO: Team info con mejor contraste */
  .team-info {
    font-size: 0.75rem;
    color: #b0e0bf;
    line-height: 1.6;
    text-shadow: 0 0 8px rgba(0, 255, 136, 0.2);
  }

  .team-info strong { color: var(--green); text-shadow: 0 0 10px var(--green); }

  .header-timer {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.3rem;
    color: var(--green);
    text-shadow: 0 0 10px var(--green);
    letter-spacing: 0.1em;
  }

  .screen { display: none; min-height: 100vh; }
  .screen.active { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; padding-top: 5rem; padding-bottom: 2rem; }

  /* INTRO */
  #screen-intro { text-align: center; gap: 2rem; animation: fadeIn 1s ease; }

  .logo {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(2rem, 5vw, 4rem);
    font-weight: 900;
    color: var(--green);
    text-shadow: 0 0 20px var(--green), 0 0 60px #00ff8840;
    letter-spacing: 0.2em;
    animation: pulse-glow 2.5s ease-in-out infinite;
  }

  /* MEJORADO: Subtitle con mejor contraste */
  .subtitle {
    font-size: 0.85rem;
    color: #00ff88;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    text-shadow: 0 0 10px rgba(0, 255, 136, 0.2);
  }

  /* MEJORADO: Intro text con mejor contraste */
  .intro-text {
    max-width: 580px;
    line-height: 1.9;
    color: #c8ffdf;
    border: 1px solid var(--border);
    padding: clamp(1.2rem, 4vw, 1.8rem) clamp(1.2rem, 4vw, 2rem);
    background: var(--panel);
    position: relative;
    font-size: clamp(0.95rem, 4vw, 1.05rem);
    text-shadow: 0 0 5px rgba(200, 255, 223, 0.1);
  }

  .intro-text::before {
    content: '// MISIÓN FINAL';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.7rem;
    letter-spacing: 0.2em;
  }

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
    max-width: 350px;
    text-align: center;
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

  /* QUIZ */
  #screen-quiz {
    gap: 1.5rem;
    max-width: 680px;
    width: 100%;
    animation: fadeIn 0.5s ease;
  }

  .hud {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border);
    padding-bottom: 0.8rem;
  }

  .hud-label {
    font-size: 0.7rem;
    color: #00cc66;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    text-shadow: 0 0 6px rgba(0, 204, 102, 0.2);
  }

  .hud-val {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.1rem;
    color: var(--green);
    letter-spacing: 0.1em;
  }

  .progress-bar {
    width: 100%;
    height: 2px;
    background: #011a0a;
  }

  .progress-fill {
    height: 100%;
    background: var(--green);
    box-shadow: 0 0 6px var(--green);
    transition: width 0.6s ease;
  }

  .question-card {
    width: 100%;
    border: 1px solid var(--border);
    background: var(--panel);
    padding: clamp(1.5rem, 4vw, 2rem);
    position: relative;
  }

  .question-card::before {
    content: attr(data-label);
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.7rem;
    letter-spacing: 0.2em;
  }

  .riddle-box {
    font-size: 1.05rem;
    line-height: 1.9;
    color: #aaffcc;
    white-space: pre-line;
    margin-bottom: 1.8rem;
  }

  .answer-input {
    width: 100%;
    background: transparent;
    border: none;
    border-bottom: 2px solid var(--green);
    color: var(--green);
    font-family: 'Share Tech Mono', monospace;
    font-size: max(16px, 1.3rem);
    padding: 0.8rem 0.5rem;
    outline: none;
    letter-spacing: 0.15em;
    text-align: center;
    transition: border-color 0.2s;
  }

  .answer-input:focus { box-shadow: 0 2px 10px #00ff8820; }

  .answer-input.wrong {
    border-color: var(--red);
    color: var(--red);
    animation: shake 0.4s ease;
  }

  .answer-input.correct {
    border-color: var(--green);
    background: #00ff8808;
  }

  .feedback {
    text-align: center;
    font-size: 0.85rem;
    min-height: 1.4rem;
    margin-top: 0.8rem;
    letter-spacing: 0.08em;
  }

  .feedback.ok { color: var(--green); }
  .feedback.err { color: var(--red); }

  /* WIN */
  #screen-win {
    text-align: center;
    gap: 1.5rem;
    animation: fadeIn 1s ease;
  }

  .win-title {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 900;
    color: var(--green);
    text-shadow: 0 0 30px var(--green);
    animation: pulse-glow 1.5s ease-in-out infinite;
  }

  .result-panel {
    border: 1px solid var(--border);
    padding: 1.5rem 2.5rem;
    background: var(--panel);
    line-height: 2.2;
    font-size: 0.9rem;
    color: #b0e0bf;
    text-shadow: 0 0 4px rgba(176, 224, 191, 0.15);
  }

  .result-panel span { color: var(--green); font-size: 1.05rem; }

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

  /* Particles */
  .particles { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
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
  .btn { min-height: 52px; -webkit-tap-highlight-color: transparent; }
  @media (max-width: 600px) {
    #screen-quiz, .hud, .progress-bar { max-width: 100%; }
    .screen.active { padding-left: 1rem; padding-right: 1rem; padding-top: 5.5rem; }
    .game-header { padding: 0.6rem 1rem; }
    .team-info { font-size: 0.7rem; }
    .header-timer { font-size: 1.1rem; }
    .riddle-box { font-size: 0.95rem; }
    .answer-input { font-size: max(16px, 1.1rem); }
    .btn { font-size: 0.8rem; letter-spacing: 0.1em; padding: 0.8rem 1rem; }
    .intro-text { font-size: 0.95rem; }
  }
</style>
</head>
<body>

@include('components.game-audio-setup', ['trackKey' => 'prueba8'])

<!-- HEADER CON INFO DE EQUIPO Y TIMER -->
<div class="game-header">
  <div class="team-info">
    <strong>Equipo:</strong> {{ session('nombre_equipo', 'Sin equipo') }}<br>
    <strong>Clase:</strong> {{ session('clase', 'N/A') }}
  </div>
  <div class="header-timer" id="cronometro">--:--</div>
</div>

<div class="particles" id="particles"></div>

<!-- INTRO -->
<div class="screen active" id="screen-intro">
  <div class="logo">CORTAFUEGOS</div>
  <div class="subtitle">// BARRERA INTELECTUAL</div>
  <div class="intro-text">
    <p>Agente, estás a un paso de recuperar el código fuente central del escape room.</p>
    <br>
    <p>El sistema de seguridad final requiere que demuestres tus conocimientos humanos fundamentales para diferenciarte de las IAs extractoras.</p>
    <br>
    <p style="color:#445544">Supera el interrogatorio y las puertas se abrirán.</p>
  </div>
  <button class="btn" onclick="startGame()">▶ INICIAR CONEXIÓN</button>
</div>

<!-- QUIZ -->
<div class="screen" id="screen-quiz">
  <div class="hud" style="width:100%; max-width:680px;">
    <div>
      <div class="hud-label">Pregunta</div>
      <div class="hud-val" id="q-counter">1 / 5</div>
    </div>
  </div>
  <div style="width:100%; max-width:680px;">
    <div class="progress-bar"><div class="progress-fill" id="progress-fill" style="width:0%"></div></div>
  </div>

  <div class="question-card" id="question-card" data-label="// ANALIZANDO...">
    <div class="riddle-box" id="riddle-box"></div>
    <input class="answer-input" id="answer-input" type="text" placeholder="_ _ _ _ _ _ _" autocomplete="off" spellcheck="false" autocapitalize="off">
    <div class="feedback" id="feedback"></div>
    <br>
    <button id="verify-btn" class="btn" onclick="checkAnswer()" style="width:100%; margin-top:0.5rem">VERIFICAR →</button>
  </div>
</div>

<!-- WIN -->
<div class="screen" id="screen-win">
  <div style="font-size:3.5rem">🔓</div>
  <div class="win-title">ACCESO MAESTRO CONCEDIDO</div>
  <div class="subtitle">// Firewall Colapsado Integrado</div>
  <div class="result-panel" id="win-stats"></div>
  <a href="/prueba9" class="btn" id="btn-continuar" style="pointer-events:none; opacity:0.4;">VERIFICANDO CON EL SERVIDOR...</a>
</div>

<script>
// ── INYECCIÓN PREGUNTAS BBDD ────────────────────────────────
const questions = JSON.parse(document.querySelector('meta[name="quiz-data"]').getAttribute('content'));

// ── TIMER DESDE BBDD ────────────────────────────────────────
let tiempoInicial = parseInt("{{ $tiempo_transcurrido ?? 0 }}");
const loadTimeMs = Date.now();
const timerDisplay = document.getElementById('cronometro');
const MAX_MINUTOS = 90;

function actualizarTimer() {
  let tiempo = tiempoInicial + Math.floor((Date.now() - loadTimeMs) / 1000);
  let minutos = parseInt(tiempo / 60, 10);
  let segundos = parseInt(tiempo % 60, 10);

  let m = minutos  < 10 ? "0" + minutos  : minutos;
  let s = segundos < 10 ? "0" + segundos : segundos;

  if(timerDisplay) timerDisplay.textContent = m + ":" + s;

  if (minutos >= MAX_MINUTOS) {
      window.location.href = '/final';
      return;
  }
}

actualizarTimer();
setInterval(actualizarTimer, 1000);

// ── ESTADO ───────────────────────────────────────────────────
let currentQ = 0;
let isTransitioning = false;
let userAnswers = [];

// ── PARTÍCULAS ───────────────────────────────────────────────
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

// ── HELPERS ──────────────────────────────────────────────────
function showScreen(id) {
  document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
  document.getElementById(id).classList.add('active');
}

function normalize(str) {
  return str.trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
}

// ── GAME ─────────────────────────────────────────────────────
function startGame() {
  currentQ = 0;
  isTransitioning = false;
  userAnswers = [];
  showScreen('screen-quiz');
  loadQuestion();
}

function loadQuestion() {
  const q = questions[currentQ];
  document.getElementById('question-card').setAttribute('data-label', q.label);
  document.getElementById('riddle-box').innerText = q.riddle;

  const inp = document.getElementById('answer-input');
  const btn = document.getElementById('verify-btn');

  inp.value = '';
  inp.className = 'answer-input';
  inp.disabled = false;
  if(btn) btn.disabled = false;

  document.getElementById('feedback').textContent = '';
  document.getElementById('feedback').className = 'feedback';
  document.getElementById('q-counter').textContent = `${currentQ + 1} / ${questions.length}`;
  document.getElementById('progress-fill').style.width = (currentQ / questions.length * 100) + '%';

  inp.onkeydown = e => {
      if (e.key === 'Enter') {
          e.preventDefault();
          checkAnswer();
      }
  };
  inp.focus();

  isTransitioning = false;
}

function checkAnswer() {
  if (isTransitioning) return;

  const input = document.getElementById('answer-input');
  const btn = document.getElementById('verify-btn');
  const val = input.value;

  if (!val.trim()) {
      input.classList.add('wrong');
      document.getElementById('feedback').className = 'feedback err';
      document.getElementById('feedback').textContent = '[ERROR] No se han detectado datos de entrada.';
      setTimeout(() => {
        input.classList.remove('wrong');
        document.getElementById('feedback').textContent = '';
      }, 1500);
      return;
  }

  const q = questions[currentQ];
  const userAns = normalize(val);
  
  const alternatives = [];
  if (q.alternatives && q.alternatives.length > 0) {
      q.alternatives.forEach(a => alternatives.push(normalize(a)));
  } else {
      String(q.answer || '').split(',').forEach(a => alternatives.push(normalize(a)));
  }
  alternatives.push(normalize(q.answer || ''));

  const isCorrect = alternatives.includes(userAns);

  if (isCorrect) {
    isTransitioning = true;
    input.disabled = true;
    input.onkeydown = null;
    if(btn) btn.disabled = true;

    userAnswers.push(val);

    input.classList.add('correct');
    document.getElementById('feedback').className = 'feedback ok';
    document.getElementById('feedback').textContent = '[OK] Parámetro válido — Nodos activados';
    if (window.audioManager) window.audioManager.playSFX('correct');
    setTimeout(() => {
      currentQ++;
      if (currentQ >= questions.length) showWin();
      else loadQuestion();
    }, 1500);
  } else {
    isTransitioning = true;
    input.disabled = true;
    if(btn) btn.disabled = true;

    input.classList.add('wrong');
    document.getElementById('feedback').className = 'feedback err';
    document.getElementById('feedback').textContent = '[ERROR] Acceso denegado. Intuitiva incorrecta.';
    if (window.audioManager) window.audioManager.playSFX('error');
    setTimeout(() => {
      input.classList.remove('wrong');
      input.value = '';
      input.disabled = false;
      if(btn) btn.disabled = false;
      document.getElementById('feedback').textContent = '';
      isTransitioning = false;
      input.focus();
    }, 1500);
  }
}

function showWin() {
  document.getElementById('progress-fill').style.width = '100%';
  document.getElementById('win-stats').innerHTML =
    `Niveles de Conocimiento comprobados: <span>${questions.length} / ${questions.length}</span><br><br>` +
    `<span style="font-size:0.8rem; color:#445544">// ENVIANDO HASH DE VALIDACIÓN AL BACKEND...</span>`;
  showScreen('screen-win');

  // Iniciar validación con reintentos automáticos
  sendValidation(1);
}

/**
 * Envía la validación al backend con reintentos automáticos.
 * NUNCA recarga la página — preserva userAnswers para no perder progreso.
 */
function sendValidation(attempt) {
  var maxAttempts = 3;
  var btnContinuar = document.getElementById('btn-continuar');

  // Feedback visual durante el intento
  if (attempt > 1) {
    document.getElementById('win-stats').innerHTML =
      `Niveles de Conocimiento comprobados: <span>${questions.length} / ${questions.length}</span><br><br>` +
      `<span style="font-size:0.8rem; color:#ffaa00;">// REINTENTANDO VALIDACIÓN (${attempt}/${maxAttempts})...</span>`;
  }

  btnContinuar.style.pointerEvents = 'none';
  btnContinuar.style.opacity = '0.4';
  btnContinuar.textContent = 'VERIFICANDO CON EL SERVIDOR...';

  fetch("/prueba8", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      completed: true,
      respuestas_usuario: userAnswers
    })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      // ✅ Validación exitosa — habilitar botón de continuar
      document.getElementById('win-stats').innerHTML =
        `Niveles de Conocimiento comprobados: <span>${questions.length} / ${questions.length}</span><br><br>` +
        `<span style="font-size:0.8rem; color:var(--green);">// VALIDACIÓN COMPLETADA ✓</span>`;
      btnContinuar.textContent = 'CONTINUAR A LA SIGUIENTE PRUEBA →';
      btnContinuar.href = '/prueba9';
      btnContinuar.onclick = null;
      btnContinuar.style.pointerEvents = 'auto';
      btnContinuar.style.opacity = '1';
    } else {
      // ❌ Servidor rechazó — reintentar automáticamente o mostrar botón manual
      handleValidationFailure(attempt, maxAttempts, 'El servidor rechazó la validación.');
    }
  })
  .catch(function(err) {
    console.error('Error de red al validar (intento ' + attempt + '):', err);
    // ❌ Error de red — reintentar automáticamente o mostrar botón manual
    handleValidationFailure(attempt, maxAttempts, 'Error de conexión con el servidor.');
  });
}

/**
 * Gestiona fallos de validación: reintenta automáticamente o muestra
 * un botón de reintento manual. NUNCA redirige a /prueba8 (no recarga).
 */
function handleValidationFailure(attempt, maxAttempts, errorMsg) {
  var btnContinuar = document.getElementById('btn-continuar');

  if (attempt < maxAttempts) {
    // Reintentar automáticamente con backoff exponencial (2s, 4s)
    var delay = 2000 * attempt;
    document.getElementById('win-stats').innerHTML =
      `Niveles de Conocimiento comprobados: <span>${questions.length} / ${questions.length}</span><br><br>` +
      `<span style="font-size:0.8rem; color:#ffaa00;">// ${errorMsg} Reintentando en ${delay/1000}s...</span>`;
    setTimeout(function() { sendValidation(attempt + 1); }, delay);
  } else {
    // Todos los reintentos agotados — botón manual (SIN recargar página)
    document.getElementById('win-stats').innerHTML =
      `Niveles de Conocimiento comprobados: <span>${questions.length} / ${questions.length}</span><br><br>` +
      `<span style="color:var(--red);">${errorMsg}</span><br>` +
      `<span style="font-size:0.8rem; color:#888;">Tus respuestas están guardadas. Pulsa el botón para reintentar.</span>`;
    btnContinuar.textContent = '↻ REINTENTAR VALIDACIÓN';
    btnContinuar.removeAttribute('href');
    btnContinuar.onclick = function(e) {
      e.preventDefault();
      sendValidation(1);
    };
    btnContinuar.style.pointerEvents = 'auto';
    btnContinuar.style.opacity = '1';
  }
}
</script>
</body>
</html>

