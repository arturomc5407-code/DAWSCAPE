<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ESCAPE ROOM | DAWSCAPE</title>
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
    font-size: clamp(2.2rem, 6vw, 4.5rem);
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
    content: '// MISIÓN';
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

  /* MEJORADO: HUD label con mejor contraste */
  .hud-label {
    font-size: 0.7rem;
    color: #00ff88;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    text-shadow: 0 0 6px rgba(0, 255, 136, 0.15);
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
    font-size: max(16px, 1.3rem); /* Prevents iOS zoom on focus */
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

  /* MEJORADO: Result panel con mejor contraste */
  .result-panel {
    border: 1px solid var(--border);
    padding: 1.5rem 2.5rem;
    background: var(--panel);
    line-height: 2.2;
    font-size: 0.9rem;
    color: #b0e0bf;
    text-shadow: 0 0 5px rgba(200, 255, 223, 0.1);
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
    #screen-quiz { max-width: 100%; }
    .screen.active { padding-left: 1rem; padding-right: 1rem; padding-top: 5.5rem; }
    .game-header { padding: 0.6rem 1rem; }
    .team-info { font-size: 0.7rem; }
    .header-timer { font-size: 1.1rem; }
    .riddle-box { font-size: 0.95rem; }
    .answer-input { font-size: max(16px, 1.1rem); letter-spacing: 0.05em; }
    .btn { font-size: 0.8rem; letter-spacing: 0.1em; padding: 0.8rem 1rem; }
  }
</style>
</head>
<body>

@include('components.game-audio-setup', ['trackKey' => 'prueba1'])

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
  <div class="logo">LAB 115</div>
  <div class="subtitle">// Sistema de Escape · Nivel Clasificado</div>
  <div class="intro-text">
    <p>Agente, el sistema de seguridad del <strong style="color:var(--green)">Aula de Informática</strong> ha sido activado.</p>
    <br>
    <p>Para desbloquear la salida, deberás resolver <strong style="color:var(--green)">6 acertijos</strong> ocultos en esta sala. Observa bien cada rincón.</p>
    <br>
    <p style="color:#445544">Sin pistas. Sin ayuda. Solo tus ojos y tu ingenio.</p>
  </div>
  <button class="btn" onclick="startGame()">▶ COMENZAR</button>
</div>

<!-- QUIZ -->
<div class="screen" id="screen-quiz">
  <div class="hud" style="width:100%; max-width:680px;">
    <div>
      <div class="hud-label">Prueba</div>
      <div class="hud-val" id="q-counter">1 / 6</div>
    </div>
  </div>
  <div style="width:100%; max-width:680px;">
    <div class="progress-bar"><div class="progress-fill" id="progress-fill" style="width:0%"></div></div>
  </div>

  <div class="question-card" id="question-card" data-label="// ACERTIJO">
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
  <div class="win-title">ACCESO CONCEDIDO</div>
  <div class="subtitle">// Todos los sistemas desbloqueados</div>
  <div class="result-panel" id="win-stats"></div>
  <a href="{{ route('prueba2') }}" class="btn" id="btn-continuar" style="pointer-events:none; opacity:0.4;">VERIFICANDO CON EL SERVIDOR...</a>
</div>

<script>
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

// ── PREGUNTAS ────────────────────────────────────────────────
const questions = [
  {
    label: "// ACERTIJO 01",
    riddle: `Soy un número de tres cifras.\nSoy la puerta de entrada a esta sala.\nNo soy el cien, ni el doscientos,\npero aquí dentro guardo todos tus momentos.\n\n¿Cuál es mi número?`,
    answer: "115",
    alt: ["aula 115"]
  },
  {
    label: "// ACERTIJO 02",
    riddle: `Tengo cuerpo pero no huesos.\nProceso datos, no tengo voz.\nSomos un grupo par y exacto en esta sala.\n\n¿Cuántos somos?`,
    answer: "16",
    alt: ["dieciséis", "dieciseis"]
  },
  {
    label: "// ACERTIJO 03",
    riddle: `Mostramos mundos que no existen,\nsomos el doble de nuestros compañeros.\nAlgunas van en parejas, otras solas,\npero juntas sumamos un número certero.\n\n¿Cuántas somos en total?`,
    answer: "32",
    alt: ["treinta y dos"]
  },
  {
    label: "// ACERTIJO 04",
    riddle: `Vivo en el techo, nunca en el suelo.\nSin mí esta sala estaría a oscuras.\nSomos pares como alas de un pájaro,\ny alumbramos cada rincón con frescura.\n\n¿Cuántos somos?`,
    answer: "14",
    alt: ["catorce"]
  },
  {
    label: "// ACERTIJO 05",
    riddle: `Soy el guardián del aula. Mi hogar es el lugar más visible de todos, pero aguardo oculto a un costado, a espaldas de la pizarra.\n\nNo soy un número ni una palabra, sino una cadena de caracteres que nació en fábrica.\n\nEncuéntrame. Léeme. Introdúceme.\n\n¿Cuál es mi código?`,
    answer: "5x0lh8bx100127t",
    alt: ["5x0lh8bx100127t"]
  },
  {
    label: "// ACERTIJO FINAL",
    riddle: `Has llegado lejos, agente. Pero el último candado requiere lógica pura.\n\nSuma todos los números que has descubierto: el código de la sala, las máquinas, las pantallas y las luces.\n\n¿Cuál es el código de salida?`,
    answer: "177",
    alt: ["ciento setenta y siete"]
  }
];

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

  // Solo se bindea cuando el componente está libre
  inp.onkeydown = e => {
      if (e.key === 'Enter') {
          e.preventDefault(); // Evitamos acciones nativas
          checkAnswer();
      }
  };
  inp.focus();

  isTransitioning = false; // Liberamos el candado
}

function checkAnswer() {
  if (isTransitioning) return; // IGNORAR CUALQUIER CLICK O ENTER SI HAY TRANSICIÓN

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
      return; // IMPIDE AVANZAR SI ESTÁ VACÍO
  }

  const q = questions[currentQ];
  const userAns = normalize(val);
  const correct = normalize(q.answer);
  const alts = (q.alt || []).map(normalize);

  if (userAns === correct || alts.includes(userAns)) {
    isTransitioning = true; // ACTIVAMOS CANDADO
    input.disabled = true;  // BLOQUEO TOTAL DE INPUT
    input.onkeydown = null; // ELIMINAMOS FLUSHING
    if(btn) btn.disabled = true;

    userAnswers.push(userAns);

    input.classList.add('correct');
    document.getElementById('feedback').className = 'feedback ok';
    document.getElementById('feedback').textContent = '[OK] Respuesta aceptada — Cortafuegos superado';
    if (window.audioManager) window.audioManager.playSFX('correct');
    setTimeout(() => {
      currentQ++;
      if (currentQ >= questions.length) showWin();
      else loadQuestion();
    }, 1500);
  } else {
    isTransitioning = true; // BLOQUEO TEMPORAL DURANTE EL ERROR
    input.disabled = true;
    if(btn) btn.disabled = true;

    input.classList.add('wrong');
    document.getElementById('feedback').className = 'feedback err';
    document.getElementById('feedback').textContent = '[ERROR] Respuesta incorrecta. Inténtalo de nuevo.';
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
    `Acertijos logicos eludidos: <span>${questions.length} / ${questions.length}</span><br><br>` +
    `<span style="font-size:0.8rem; color:#445544">// ENVIANDO VALIDACIÓN AL BACKEND...</span>`;
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
      `Acertijos logicos eludidos: <span>${questions.length} / ${questions.length}</span><br><br>` +
      `<span style="font-size:0.8rem; color:#ffaa00;">// REINTENTANDO VALIDACIÓN (${attempt}/${maxAttempts})...</span>`;
  }

  btnContinuar.style.pointerEvents = 'none';
  btnContinuar.style.opacity = '0.4';
  btnContinuar.textContent = 'VERIFICANDO CON EL SERVIDOR...';

  fetch("/prueba1", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
      completed: true,
      answers: userAnswers
    })
  })
  .then(function(r) { return r.json().catch(function() { return {success: false, message: 'Respuesta inválida del servidor'}; }); })
  .then(function(data) {
    if (data.success) {
      // ✅ Validación exitosa — habilitar botón de continuar
      document.getElementById('win-stats').innerHTML =
        `Acertijos logicos eludidos: <span>${questions.length} / ${questions.length}</span><br><br>` +
        `<span style="font-size:0.8rem; color:var(--green);">// VALIDACIÓN COMPLETADA ✓</span>`;
      btnContinuar.textContent = 'PASAR A LA SIGUIENTE PRUEBA →';
      btnContinuar.style.pointerEvents = 'auto';
      btnContinuar.style.opacity = '1';
    } else {
      handleValidationFailure(attempt, maxAttempts, 'El servidor rechazó la validación.');
    }
  })
  .catch(function(err) {
    console.error('Error de red al validar (intento ' + attempt + '):', err);
    handleValidationFailure(attempt, maxAttempts, 'Error de conexión con el servidor.');
  });
}

/**
 * Gestiona fallos de validación: reintenta automáticamente o muestra
 * un botón de reintento manual. NUNCA redirige/recarga la página.
 */
function handleValidationFailure(attempt, maxAttempts, errorMsg) {
  var btnContinuar = document.getElementById('btn-continuar');

  if (attempt < maxAttempts) {
    // Reintentar automáticamente con backoff exponencial (2s, 4s)
    var delay = 2000 * attempt;
    document.getElementById('win-stats').innerHTML =
      `Acertijos logicos eludidos: <span>${questions.length} / ${questions.length}</span><br><br>` +
      `<span style="font-size:0.8rem; color:#ffaa00;">// ${errorMsg} Reintentando en ${delay/1000}s...</span>`;
    setTimeout(function() { sendValidation(attempt + 1); }, delay);
  } else {
    // Todos los reintentos agotados — botón manual (SIN recargar página)
    document.getElementById('win-stats').innerHTML =
      `Acertijos logicos eludidos: <span>${questions.length} / ${questions.length}</span><br><br>` +
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

function resetGame() {
  showScreen('screen-intro');
}
</script>
</body>
</html>

@include('components.pista-ayuda', ['pruebaNumero' => 1, 'pistaConfig' => $pistaConfig, 'yaUsada' => $yaUsada])
@include('components.live-listener')
