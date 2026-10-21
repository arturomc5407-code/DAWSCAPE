<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>TERMINAL OASIS — DAWSCAPE</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');

  :root {
    --green: #00ff88;
    --red: #ff2244;
    --bg: #020c06;
    --panel: #041a0e;
    --border: #00ff8840;
    --text: #c8ffdf;
    --terminal: #0a0a0a;
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
    padding: 0.8rem 1rem;
    background: rgba(2, 12, 6, 0.95);
    border-bottom: 1px solid var(--border);
    z-index: 100;
    backdrop-filter: blur(8px);
  }

  /* MEJORADO: Team info con mejor contraste */
  .team-info {
    font-size: 0.75rem;
    color: #b0e0bf;
    line-height: 1.5;
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

  .screen {
      display: none;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      padding-top: 5rem;
      padding-bottom: 2rem;
      min-height: 100vh;
      width: 100%;
      animation: fadeIn 0.5s ease;
  }
  .screen.active { display: flex; }

  .logo {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(1.8rem, 6vw, 2.5rem);
    font-weight: 900;
    color: var(--green);
    text-shadow: 0 0 20px var(--green);
    letter-spacing: 0.1em;
    margin-bottom: 1.5rem;
    text-align: center;
  }

  /* MEJORADO: Intro text con mejor contraste */
  .intro-text {
    width: 100%;
    max-width: 600px;
    line-height: 1.6;
    color: #c8ffdf;
    border: 1px solid var(--border);
    padding: clamp(1.2rem, 4vw, 1.5rem);
    background: var(--panel);
    position: relative;
    font-size: clamp(0.95rem, 4vw, 1.05rem);
    text-shadow: 0 0 5px rgba(200, 255, 223, 0.1);
  }

  .intro-text::before {
    content: '// FIREWALL OASIS_NET';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--red);
    font-size: 0.7rem;
    letter-spacing: 0.2em;
  }

  .intro-text strong {
      color: var(--green);
      font-weight: normal;
  }

  .term-list {
      list-style-type: none;
      margin: 1.2rem 0;
      padding-left: 0.5rem;
      border-left: 2px solid var(--border);
  }

  .term-list li {
      margin-bottom: 0.8rem;
      position: relative;
      padding-left: 1rem;
  }

  .term-list li::before {
      content: '>';
      position: absolute;
      left: 0;
      color: var(--green);
  }

  .form-group {
    margin-top: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
    width: 100%;
  }

  /* BIGGER INPUT FOR MOBILE */
  .answer-input {
    width: 100%;
    max-width: 350px;
    background: var(--terminal);
    border: 2px solid var(--border);
    color: var(--green);
    font-family: 'Share Tech Mono', monospace;
    font-size: max(16px, clamp(1.5rem, 6vw, 2.2rem)); /* Prevents iOS zoom */
    padding: 1rem;
    border-radius: 8px;
    outline: none;
    letter-spacing: 0.2em;
    text-align: center;
    transition: all 0.2s;
  }

  .answer-input:focus {
      box-shadow: 0 0 20px rgba(0,255,136,0.3);
      border-color: var(--green);
  }

  .answer-input.wrong {
    border-color: var(--red);
    color: var(--red);
    animation: shake 0.4s ease;
  }

  .answer-input.correct {
    border-color: var(--green);
    background: #00ff8811;
  }

  /* BIGGER BUTTON FOR MOBILE */
  .btn {
    font-family: 'Orbitron', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    padding: 1.2rem;
    border: 2px solid var(--green);
    background: #00ff8811;
    color: var(--green);
    border-radius: 8px;
    cursor: pointer;
    text-transform: uppercase;
    transition: all 0.25s;
    position: relative;
    overflow: hidden;
    width: 100%;
    max-width: 350px;
  }

  .btn:active {
      transform: scale(0.98);
      background: var(--green);
      color: var(--bg);
  }

  .btn:disabled { color: #33aa66; border-color: rgba(51,170,102,0.4); background: rgba(0,255,136,0.05); cursor: not-allowed; pointer-events: none; }

  .feedback {
    text-align: center;
    font-size: 0.9rem;
    min-height: 1.5rem;
    margin-top: 0.2rem;
    letter-spacing: 0.05em;
    width: 100%;
  }
  .feedback.ok { color: var(--green); }
  .feedback.err { color: var(--red); }

  .hint-box {
      display: none;
      margin-top: 1.5rem;
      padding: 1.2rem;
      border: 1px dashed #aa9900;
      color: #ddcc44;
      font-size: 0.95rem;
      line-height: 1.5;
      background: rgba(40,40,0,0.6);
      animation: fadeIn 0.5s ease;
      border-radius: 6px;
  }

  /* TERMINAL WIN EFFECT */
  #term-win {
      width: 100%;
      max-width: 600px;
      background: var(--terminal);
      border: 1px solid var(--green);
      padding: 1.5rem;
      font-size: clamp(1rem, 4vw, 1.1rem);
      line-height: 1.8;
      color: var(--green);
      box-shadow: inset 0 0 20px rgba(0,255,136,0.1);
      border-radius: 8px;
  }

  .term-line { opacity: 0; }
  .term-line.show { opacity: 1; animation: slideRight 0.3s ease; }
  .term-line.narrative { color: #88ccaa; margin-top: 1.5rem; border-top: 1px dashed var(--border); padding-top: 1.5rem;}

  /* ANIMATIONS */
  @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
  @keyframes slideRight { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
  @keyframes shake {
    0%,100% { transform: translateX(0); }
    20% { transform: translateX(-8px); }
    40% { transform: translateX(8px); }
    60% { transform: translateX(-5px); }
    80% { transform: translateX(5px); }
  }
</style>
</head>
<body>

<div class="game-header">
  <div class="team-info">
    <strong>Equipo:</strong> {{ session('nombre_equipo', 'Sin equipo') }}<br>
    <strong>Clase:</strong> {{ session('clase', 'N/A') }}
  </div>
  <div class="header-timer" id="cronometro">--:--</div>
</div>

<!-- PANEL HACKING (MAIN) -->
<div class="screen active" id="screen-hacking">
  <div class="logo">TERMINAL OASIS</div>
  <div class="intro-text">
    <p style="color:var(--red)">Acceso al núcleo denegado.</p>
    <p style="margin-top:0.5rem;">El cortafuegos requiere un código de verificación basado en los datos de las máquinas de la planta baja.</p>
    <p style="margin-top:0.5rem; color:#668877;">Para generar el código de bypass, combina estos tres factores:</p>

    <ul class="term-list">
        <li>El precio en céntimos del producto más goloso de la playa....</li>
        <li>El número de tipos de productos diferentes que caben en la misma fila donde está ese producto que nos recuerda a la naturaleza.</li>
        <li>El número del código del mismo producto que estamos hablando, el cual lleva cacao</li>
    </ul>

    <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9em;"><strong>Introduce la suma total para purgar el sistema.</strong></p>

    <div class="form-group">
        <label style="font-size: 0.8rem; color: #445544;">// WAITING_CHECKSUM</label>
        <input class="answer-input" id="checksum-input" type="number" inputmode="numeric" pattern="[0-9]*" placeholder="___" autocomplete="off">
        <div class="feedback" id="feedback-msg"></div>
        <button class="btn" id="btn-verificar" onclick="verificarChecksum()">EJECUTAR BYPASS →</button>
    </div>

    <div class="hint-box" id="hint-box">
        <strong>[PISTA DEL SISTEMA]</strong><br>La respuesta es la suma de tres números: el precio en céntimos de la palmera de chocolate (130), el número de tipos de producto en esa fila (7) y el código del producto de la palmera (31). Súmalos.
    </div>
  </div>
</div>

<!-- TERMINAL WIN -->
<div class="screen" id="screen-win">
  <div class="logo">OASIS HACKEADO</div>
  <div id="term-win">
      <div class="term-line" id="l1">[OK] Validating checksum... 100%</div>
      <div class="term-line" id="l2">[OK] Inyección de código completada en el sector Oasis.</div>
      <div class="term-line" id="l3">[OK] Acceso concedido al núcleo del sistema DAW.</div>
      <div class="term-line narrative" id="l4">-- NO MESSAGE --</div>
      <div class="term-line" id="l5" style="margin-top: 2rem; text-align: center;">
          <a href="{{ route('prueba5') }}" class="btn" style="text-decoration:none; display:block;">PASAR A LA SIGUIENTE PRUEBA →</a>
      </div>
  </div>
</div>

<!-- Iconos Phosphor -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>

<script>
// --- TIMER LOGIC ---
let tiempo = parseInt("{{ $tiempo_transcurrido ?? 0 }}");
const timerDisplay = document.getElementById('cronometro');
const MAX_MINUTOS = 90;

function actualizarTimer() {
  let minutos = parseInt(tiempo / 60, 10);
  let segundos = parseInt(tiempo % 60, 10);
  let m = minutos < 10 ? "0" + minutos : minutos;
  let s = segundos < 10 ? "0" + segundos : segundos;
  timerDisplay.textContent = m + ":" + s;

  if (minutos >= MAX_MINUTOS) {
      window.location.href = '/final';
      return;
  }
  tiempo++;
}
actualizarTimer();
setInterval(actualizarTimer, 1000);

// --- GAME LOGIC ---
let failedAttempts = 0;
let isProcessing = false;
const input = document.getElementById('checksum-input');
const btn = document.getElementById('btn-verificar');
const feedback = document.getElementById('feedback-msg');

input.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        input.blur(); // Minimiza el teclado en móviles
        verificarChecksum();
    }
});

function verificarChecksum() {
    if (isProcessing) return;
    const value = input.value.trim();

    if (value === "") {
        showError("Introduce un número válido.");
        return;
    }

    isProcessing = true;
    input.blur();
    input.disabled = true;
    btn.disabled = true;
    feedback.textContent = "Analizando bloque de datos...";
    feedback.className = "feedback";

    fetch('/prueba4', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ checksum: value })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            feedback.textContent = "[OK] SUMA DE VERIFICACIÓN ACEPTADA.";
            feedback.className = "feedback ok";
            input.classList.add('correct');

            setTimeout(() => {
                triggerWinSequence(data.mensaje);
            }, 800);
        } else {
            failedAttempts++;
            showError("[ERROR] Checksum inválido.");

            if (failedAttempts >= 5) {
                document.getElementById('hint-box').style.display = 'block';
            }
        }
    })
    .catch(error => {
        showError("Error de comunicación.");
    });
}

function showError(msg) {
    input.classList.add('wrong');
    feedback.textContent = msg;
    feedback.className = "feedback err";

    // Vibración haptica sutil en móviles que lo soporten
    if(navigator.vibrate) navigator.vibrate(200);

    setTimeout(() => {
        input.classList.remove('wrong');
        input.value = '';
        input.disabled = false;
        btn.disabled = false;
        feedback.textContent = "";
        isProcessing = false;
        // input.focus(); omitido intencionadamente en mobile para no sacar el teclado solo
    }, 1200);
}

function triggerWinSequence(mensajeNarrativo) {
    document.getElementById('screen-hacking').classList.remove('active');
    document.getElementById('screen-win').classList.add('active');

    // Inyectamos el texto final desde backend
    document.getElementById('l4').innerText = mensajeNarrativo;

    // Secuencia de terminal delay
    const lines = [
        { id: 'l1', delay: 400 },
        { id: 'l2', delay: 1200 },
        { id: 'l3', delay: 1800 },
        { id: 'l4', delay: 2800 },
        { id: 'l5', delay: 3500 }
    ];

    lines.forEach(line => {
        setTimeout(() => {
            document.getElementById(line.id).classList.add('show');
        }, line.delay);
    });
}
</script>
</body>
</html>
