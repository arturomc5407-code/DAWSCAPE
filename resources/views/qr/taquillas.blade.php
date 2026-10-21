<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SISTEMA ESPEJO | DAWSCAPE</title>
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
      padding: 1rem;
      padding-top: 5rem;
      padding-bottom: 2rem;
      min-height: 100vh;
      width: 100%;
      animation: fadeIn 0.5s ease;
  }
  .screen.active { display: flex; }

  .logo {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(1.6rem, 5vw, 2.2rem);
    font-weight: 900;
    color: var(--green);
    text-shadow: 0 0 20px var(--green);
    letter-spacing: 0.1em;
    margin-bottom: 1rem;
    text-align: center;
    text-transform: uppercase;
  }

  /* MEJORADO: Intro text con mejor contraste */
  .intro-text {
    width: 100%;
    max-width: 600px;
    line-height: 1.5;
    color: #c8ffdf;
    border: 1px solid var(--border);
    padding: 1.2rem;
    background: var(--panel);
    position: relative;
    font-size: clamp(0.9rem, 4vw, 1rem);
    text-shadow: 0 0 5px rgba(200, 255, 223, 0.1);
  }

  .intro-text::before {
    content: '// FIREWALL ALMACÉN';
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
      margin: 1rem 0;
      padding-left: 0.5rem;
      border-left: 2px solid var(--border);
      font-size: 0.9em;
  }

  .term-list li {
      margin-bottom: 0.6rem;
      position: relative;
      padding-left: 0.8rem;
  }

  .term-list li::before {
      content: '>';
      position: absolute;
      left: 0;
      color: var(--green);
  }

  .conversion-table {
      margin: 1.5rem 0;
      padding: 1rem;
      background: var(--terminal);
      border: 1px dashed var(--green);
      text-align: center;
      font-family: 'Orbitron', sans-serif;
      box-shadow: inset 0 0 10px rgba(0,255,136,0.1);
  }

  .conversion-code {
      color: var(--green);
      font-size: 1.1rem;
      letter-spacing: 0.1em;
      margin-bottom: 0.5rem;
  }

  .conversion-target {
      color: #88ccaa;
      font-size: 0.95rem;
  }

  .form-group {
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    align-items: center;
    width: 100%;
  }

  .answer-input {
    width: 100%;
    max-width: 350px;
    background: var(--terminal);
    border: 2px solid var(--border);
    color: var(--green);
    font-family: 'Share Tech Mono', monospace;
    font-size: max(16px, clamp(1.5rem, 6vw, 2rem)); /* Prevents iOS zoom */
    padding: 0.8rem;
    border-radius: 8px;
    outline: none;
    letter-spacing: 0.1em;
    text-align: center;
    transition: all 0.2s;
  }

  .answer-input::placeholder {
      color: #00ff8840;
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
    letter-spacing: 0.1em;
    padding: 1rem;
    border: 2px solid var(--green);
    background: #00ff8811;
    color: var(--green);
    border-radius: 8px;
    cursor: pointer;
    text-transform: uppercase;
    transition: all 0.25s;
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
    font-size: 0.85rem;
    min-height: 1.2rem;
    margin-top: 0.2rem;
    letter-spacing: 0.05em;
    width: 100%;
  }
  .feedback.ok { color: var(--green); }
  .feedback.err { color: var(--red); }

  .hint-box {
      display: none;
      margin-top: 1rem;
      padding: 1rem;
      border: 1px dashed #aa9900;
      color: #ddcc44;
      font-size: 0.9rem;
      line-height: 1.4;
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
      font-size: clamp(0.9rem, 4vw, 1rem);
      line-height: 1.6;
      color: var(--green);
      box-shadow: inset 0 0 20px rgba(0,255,136,0.1);
      border-radius: 8px;
  }

  .term-line { opacity: 0; margin-bottom: 0.5rem; }
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
  <div class="logo">SISTEMA ESPEJO ACTIVADO</div>
  <div class="intro-text">
    <p>El abecedario ha sido invertido para proteger la clave. La correspondencia original ha sido corrompida.</p>
    <p style="margin-top:0.5rem; color:#668877;">Nueva tabla de conversión:</p>
    <p style="color:var(--green); letter-spacing:0.1em; text-align:center; padding: 0.5rem; background: var(--terminal); border: 1px dashed #00ff8840; margin: 0.5rem 0;">Z = 1 &nbsp; Y = 2 &nbsp; X = 3 &nbsp; ... &nbsp; A = 26</p>
    <p style="margin-top:0.8rem; color:var(--red); font-weight:bold; text-align:center; border:1px dashed var(--red); padding:0.6rem; background:rgba(255,34,68,0.05);">RECUERDA QUE DEBES USAR EL ABECEDARIO INGLÉS (26 LETRAS)</p>
    <p style="margin-top:0.5rem;">El núcleo del sistema está oculto en varias unidades numeradas del 1 al 64. Cada número representa una letra bajo el Protocolo Espejo.</p>

    <div style="margin-top: 1.5rem; border-top: 1px solid var(--border); padding-top: 1rem;">
        <p style="color:var(--red); font-size: 0.9em;">// INSTRUCCIONES DEL SISTEMA:</p>
        <ul class="term-list">
            <li>Buscar las taquillas que tienen pegadas el número, del 1 al 7. </li>
            <li>Traducir el número de taquilla a su letra .</li>
            <li>Recuperar las letras y ponerlas siguiendo el orden del 1 al 7.</li>
        </ul>
    </div>

    <div class="conversion-table">
        <div class="conversion-target">→ Ubicación a visitar: OASIS de la P2 (Planta 2)</div>
        <div style="color: var(--green); font-size: 1.05rem; margin-top: 0.5rem; font-weight:bold;">Dirígete al OASIS de la P2 y descúbrelo por ti mismo</div>
    </div>

    <p style="text-align: center; font-size: 0.9em;"><strong>Introduce la palabra clave reconstruida:</strong></p>

    <div class="form-group">
        <label style="font-size: 0.8rem; color: #445544; display:none;">// INPUT</label>
        <input class="answer-input" id="checksum-input" type="text" placeholder="_______" autocomplete="off" spellcheck="false" autocapitalize="off" maxlength="7">
        <div class="feedback" id="feedback-msg"></div>
        <button class="btn" id="btn-verificar" onclick="verificarChecksum()">DESCIFRAR NÚCLEO →</button>
    </div>

    <div class="hint-box" id="hint-box">
        <strong>[PISTA DEL SISTEMA]</strong><br>Tienes que mirar las taquillas del 1 al 7, arriba hay unos números. Esos números corresponden a letras usando el protocolo espejo, por ejemplo el 1 es la letra Z, el 2 la letra Y, el 3 la letra X y así sucesivamente. Recoge las letras de las taquillas correctas para formar la palabra clave.
    </div>
  </div>
</div>

<!-- TERMINAL WIN -->
<div class="screen" id="screen-win">
  <div class="logo">NÚCLEO RESTAURADO</div>
  <div id="term-win">
      <div class="term-line" id="l1">[OK] Descifrando alfabeto espejo… 100%</div>
      <div class="term-line" id="l2">[OK] Fragmentos recuperados correctamente.</div>
      <div class="term-line" id="l3">[OK] Palabra clave validada.</div>
      <div class="term-line narrative" id="l4">-- NO MESSAGE --</div>
      <div class="term-line" id="l5" style="margin-top: 2rem; text-align: center;">
          <a href="{{ route('prueba6') }}" class="btn" style="text-decoration:none; display:block;">PASAR A LA SIGUIENTE PRUEBA →</a>
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
        input.blur();
        verificarChecksum();
    }
});

// Remove force uppercase

function verificarChecksum() {
    if (isProcessing) return;
    const value = input.value.trim();

    if (value === "") {
        showError("Introduce una palabra válida.");
        return;
    }

    isProcessing = true;
    input.blur();
    input.disabled = true;
    btn.disabled = true;
    feedback.textContent = "Analizando bloque de datos...";
    feedback.className = "feedback";

    fetch('/prueba5', {
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
            feedback.textContent = "[OK] PROTOCOLO ESPEJO DESCIFRADO.";
            feedback.className = "feedback ok";
            input.classList.add('correct');

            setTimeout(() => {
                triggerWinSequence(data.mensaje);
            }, 800);
        } else {
            failedAttempts++;
            showError("[ERROR] Palabra clave inválida.");

            if (failedAttempts >= 5) {
                document.getElementById('hint-box').style.display = 'block';
            }
        }
    })
    .catch(error => {
        showError("Error de comunicación con el núcleo.");
    });
}

function showError(msg) {
    input.classList.add('wrong');
    feedback.textContent = msg;
    feedback.className = "feedback err";

    if(navigator.vibrate) navigator.vibrate(200);

    setTimeout(() => {
        input.classList.remove('wrong');
        input.value = '';
        input.disabled = false;
        btn.disabled = false;
        feedback.textContent = "";
        isProcessing = false;
    }, 1200);
}

function triggerWinSequence(mensajeNarrativo) {
    document.getElementById('screen-hacking').classList.remove('active');
    document.getElementById('screen-win').classList.add('active');

    document.getElementById('l4').innerText = mensajeNarrativo;

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
