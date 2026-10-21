<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ESCAPE ROOM — CLÚSTER FRAGMENTADO | DAWSCAPE</title>
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

  .screen { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; padding: 1.5rem; padding-top: 5rem; padding-bottom: 2rem; position: relative; z-index: 10; }

  /* ALERT BOX */
  .alert-box {
    width: 100%;
    max-width: 680px;
    border: 1px solid var(--red);
    background: rgba(255, 34, 68, 0.05);
    padding: clamp(1.2rem, 4vw, 2rem);
    position: relative;
    margin-bottom: 2rem;
    animation: fadeIn 1s ease;
  }

  .alert-box::before {
    content: '[ALERTA DE SISTEMA]';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--red);
    font-size: 0.8rem;
    font-weight: bold;
    letter-spacing: 0.1em;
    animation: blink 2s infinite;
  }

  .alert-box p {
    font-size: clamp(0.95rem, 4vw, 1.05rem);
    line-height: 1.9;
    color: #ffcccc;
    margin-bottom: 1rem;
  }

  /* INPUT AREA */
  .terminal-box {
    width: 100%;
    max-width: 680px;
    border: 1px solid var(--border);
    background: var(--panel);
    padding: clamp(1.5rem, 4vw, 2rem);
    position: relative;
    animation: fadeIn 1.5s ease;
  }

  .terminal-box::before {
    content: '// ENTRADA DE DATOS';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.7rem;
    letter-spacing: 0.2em;
  }

  /* MEJORADO: Terminal label con mejor contraste */
  .terminal-label {
    display: block;
    font-size: 0.9rem;
    color: #b0e0bf;
    margin-bottom: 1rem;
    text-align: center;
    text-shadow: 0 0 4px rgba(200, 255, 223, 0.1);
  }

  .answer-input {
    width: 100%;
    background: transparent;
    border: none;
    border-bottom: 2px solid var(--green);
    color: var(--green);
    font-family: 'Share Tech Mono', monospace;
    font-size: max(16px, clamp(1.4rem, 6vw, 1.8rem)); /* Prevents iOS zoom on focus */
    padding: 0.8rem 0.5rem;
    outline: none;
    letter-spacing: 0.5em;
    text-align: center;
    transition: border-color 0.2s;
  }

  .answer-input:focus { box-shadow: 0 2px 10px #00ff8820; }
  .answer-input::placeholder { color: #88ccaa; letter-spacing: 0.5em; } /* WCAG improved placeholder contrast */

  /* HINT AREA */
  /* MEJORADO: Hint con mejor contraste */
  .hint {
    display: none;
    margin-top: 1.5rem;
    padding: 1rem;
    border: 1px dashed #00ff88;
    color: #b0e0bf;
    font-size: 0.9rem;
    text-align: center;
    line-height: 1.6;
    animation: fadeIn 0.5s ease;
    text-shadow: 0 0 4px rgba(200, 255, 223, 0.1);
  }

  /* CONSOLE OUTPUT */
  .console-out {
    margin-top: 2rem;
    font-size: 0.9rem;
    line-height: 1.6;
    min-height: 60px;
    width: 100%;
    max-width: 680px;
  }

  .out-ok { color: var(--green); }
  .out-err { color: var(--red); }

  .submit-btn {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.95rem;
    letter-spacing: 0.15em;
    padding: 0.9rem 1.5rem;
    min-height: 52px;
    border: 2px solid var(--green);
    background: rgba(0,255,136,0.07);
    color: var(--green);
    cursor: pointer;
    text-transform: uppercase;
    transition: all 0.25s;
    position: relative;
    overflow: hidden;
    width: 100%;
    margin-top: 1.2rem;
    text-align: center;
    display: block; /* Always visible on mobile */
    -webkit-tap-highlight-color: transparent;
  }

  .submit-btn:active { background: var(--green); color: var(--bg); }
  .submit-btn:hover { background: var(--green); color: var(--bg); box-shadow: 0 0 15px var(--green); }

  .btn {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.9rem;
    letter-spacing: 0.2em;
    padding: 1rem 2.5rem;
    min-height: 52px;
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
    margin-top: 1rem;
    text-align: center;
    display: none;
    -webkit-tap-highlight-color: transparent;
  }

  .btn.visible { display: block; }

  @media (max-width: 600px) {
    .alert-box, .terminal-box, .console-out { max-width: 100% !important; }
    .screen { padding-top: 5.5rem; padding-left: 1rem; padding-right: 1rem; }
    .game-header { padding: 0.6rem 1rem; }
    .team-info { font-size: 0.7rem; }
    .header-timer { font-size: 1.1rem; }
    .answer-input { letter-spacing: 0.3em; }
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

  /* ANIMATIONS */
  @keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

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
</style>
</head>
<body>

@include('components.game-audio-setup', ['trackKey' => 'prueba2'])

<div class="game-header">
  <div class="team-info">
    <strong>Equipo:</strong> {{ session('nombre_equipo', 'Sin equipo') }}<br>
    <strong>Clase:</strong> {{ session('clase', 'N/A') }}
  </div>
  <div class="header-timer" id="cronometro">--:--</div>
</div>

<div class="particles" id="particles"></div>

<div class="screen">

  <div class="alert-box">
    <p>La clave maestra ha sido rota en pedazos por un virus informático. Los 5 fragmentos se han escondido en las máquinas expendedoras de todo el edificio.</p>
    <p><strong>Vuestra misión:</strong> Localizad las máquinas expendedoras del instituto. En total hay <strong>6 máquinas</strong>, pero <strong>la pista solo está en 5 de ellas</strong>. En alguna parte de cada una encontraréis pegada una pieza de información (una letra). Reunid las 5 letras y ordenadlas para descubrir la palabra secreta y conseguir el código de acceso.</p>
  </div>

  <div class="terminal-box">
    <label class="terminal-label">Introduce la clave maestra de 5 caracteres:</label>
    <input class="answer-input" id="answer-input" type="text" maxlength="5" placeholder="_ _ _ _ _" autocomplete="off" spellcheck="false" autocapitalize="characters" inputmode="text">
    <button class="submit-btn" id="submit-btn" onclick="submitAnswer()">▶ ENVIAR RESPUESTA</button>
  </div>

  <div class="console-out" id="console-out"></div>

  <a href="{{ route('prueba3') }}" class="btn" id="next-btn" style="pointer-events:none; opacity:0.4;">VERIFICANDO CON EL SERVIDOR...</a>

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

// ── LÓGICA DE VALIDACIÓN ─────────────────────────────────────
let failedAttempts = 0;
const correctAnswer = "cieza";
const inputField = document.getElementById('answer-input');
const consoleOut = document.getElementById('console-out');
const nextBtn = document.getElementById('next-btn');
let solved = false;

function submitAnswer() {
    if (solved) return;

    let val = inputField.value.trim().toLowerCase().replace(/\s+/g, '');

    if (val.length !== 5) {
        printError("[ERROR] Sintaxis inválida. La clave debe tener exactamente 5 caracteres.");
        return;
    }

    if (validateWord(val)) {
        solved = true;
        inputField.disabled = true;
        document.getElementById('submit-btn').disabled = true;
        if (window.audioManager) window.audioManager.playSFX('correct');
        printSuccess();
    } else {
        failedAttempts++;
        if (window.audioManager) window.audioManager.playSFX('error');
        printError("[ERROR] Clave no reconocida. Verificad los sectores de memoria.");
    }
}

inputField.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); submitAnswer(); }
});

function validateWord(word) {
    // Basic normalization handling to make sure accents don't break logic just in case,
    // though CIEZA doesn't necessarily need it, better safe than sorry.
    const normalized = word.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    return normalized === correctAnswer;
}

function printError(msg) {
    consoleOut.innerHTML = `<span class="out-err">${msg}</span>`;
    inputField.value = '';
}

function printSuccess() {
    consoleOut.innerHTML = `
        <span class="out-ok">[OK] Reconstrucción de paquetes exitosa... 100%</span><br>
        <span class="out-ok">[OK] Clave maestra aceptada.</span><br>
        <span class="out-ok">[OK] Cortafuegos perimetral desactivado.</span><br><br>
        <span style="color: #88ccaa">"Habéis restaurado el flujo de datos."</span><br>
        <span style="font-size:0.8rem; color:#445544">// ENVIANDO VALIDACIÓN AL BACKEND...</span>
    `;
    nextBtn.classList.add('visible');
    sendValidation(1);
}

/**
 * Envía la validación al backend con reintentos automáticos.
 * NUNCA recarga la página.
 */
function sendValidation(attempt) {
    var maxAttempts = 3;

    if (attempt > 1) {
        consoleOut.innerHTML = `
            <span class="out-ok">[OK] Clave maestra aceptada.</span><br>
            <span style="font-size:0.8rem; color:#ffaa00;">// REINTENTANDO VALIDACIÓN (${attempt}/${maxAttempts})...</span>
        `;
    }

    nextBtn.style.pointerEvents = 'none';
    nextBtn.style.opacity = '0.4';
    nextBtn.textContent = 'VERIFICANDO CON EL SERVIDOR...';

    fetch("/prueba2", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ answer: inputField.value.trim() })
    })
    .then(function(r) { return r.json().catch(function() { return {success: false, message: 'Respuesta inválida del servidor'}; }); })
    .then(function(data) {
        if (data.success) {
            consoleOut.innerHTML = `
                <span class="out-ok">[OK] Reconstrucción de paquetes exitosa... 100%</span><br>
                <span class="out-ok">[OK] Clave maestra aceptada.</span><br>
                <span class="out-ok">[OK] Cortafuegos perimetral desactivado.</span><br><br>
                <span style="color: #88ccaa">"Habéis restaurado el flujo de datos. Avanzad al siguiente nivel."</span><br>
                <span style="font-size:0.8rem; color:var(--green);">// VALIDACIÓN COMPLETADA ✓</span>
            `;
            nextBtn.textContent = 'PASAR A LA SIGUIENTE PRUEBA →';
            nextBtn.style.pointerEvents = 'auto';
            nextBtn.style.opacity = '1';
        } else {
            handleValidationFailure(attempt, maxAttempts, 'El servidor rechazó la validación.');
        }
    })
    .catch(function(err) {
        console.error('Error de red al validar (intento ' + attempt + '):', err);
        handleValidationFailure(attempt, maxAttempts, 'Error de conexión con el servidor.');
    });
}

function handleValidationFailure(attempt, maxAttempts, errorMsg) {
    if (attempt < maxAttempts) {
        var delay = 2000 * attempt;
        consoleOut.innerHTML = `
            <span class="out-ok">[OK] Clave maestra aceptada.</span><br>
            <span style="font-size:0.8rem; color:#ffaa00;">// ${errorMsg} Reintentando en ${delay/1000}s...</span>
        `;
        setTimeout(function() { sendValidation(attempt + 1); }, delay);
    } else {
        consoleOut.innerHTML = `
            <span style="color:var(--red);">${errorMsg}</span><br>
            <span style="font-size:0.8rem; color:#888;">Tu respuesta está guardada. Pulsa el botón para reintentar.</span>
        `;
        nextBtn.textContent = '↻ REINTENTAR VALIDACIÓN';
        nextBtn.removeAttribute('href');
        nextBtn.onclick = function(e) {
            e.preventDefault();
            sendValidation(1);
        };
        nextBtn.style.pointerEvents = 'auto';
        nextBtn.style.opacity = '1';
    }
}
</script>
</body>
</html>

@include('components.pista-ayuda', ['pruebaNumero' => 2, 'pistaConfig' => $pistaConfig, 'yaUsada' => $yaUsada])
@include('components.live-listener')
