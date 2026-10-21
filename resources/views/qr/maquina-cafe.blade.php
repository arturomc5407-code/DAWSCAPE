<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NÚCLEO DE EXTRACCIÓN — DAWSCAPE</title>
@vite(['resources/css/maquina-cafe.css'])
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
  <div class="logo">PANEL DE MANTENIMIENTO</div>
  <div class="intro-text">
    <p style="color:var(--red)">No puedes entrar al sistema.</p>
    <p>La seguridad física necesita una clave especial que se crea con los datos actuales de la máquina.</p>
    <br>
    <p>Para conseguir esa clave, mezcla estos tres datos del equipo:</p>

    <ul class="term-list">
        <li>El censo total de disparadores táctiles (botones) de la máquina.</li>
        <li>El valor nominal (en céntimos) de la esencia de cítricos (Té al Limón).</li>
        <li>El umbral de saturación de glucosa (nivel máximo de azúcar).</li>
    </ul>

    <p style="text-align: center; margin-top: 2rem;"><strong>Introduce la suma total para purgar el sistema... si te atreves.</strong></p>

    <div class="form-group">
        <label style="font-size: 0.8rem; color: #445544;">// CHECKSUM_REQUIRED</label>
        <input class="answer-input" id="checksum-input" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="___" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
        <div class="feedback" id="feedback-msg"></div>
        <button class="btn" id="btn-verificar" onclick="verificarChecksum()">VERIFICAR →</button>
    </div>

    <div class="hint-box" id="hint-box">
        <strong>[PISTA DEL SISTEMA]</strong> — La respuesta es una suma de TRES números. Cuenta bien todos los botones de la máquina (incluidos los de Start/Stop, Azúcar, etc).
    </div>
  </div>

</div>

<!-- TERMINAL WIN -->
<div class="screen" id="screen-win">
  <div class="logo">SISTEMA COMPROMETIDO</div>
  <div id="term-win">
      <div class="term-line" id="l1">[OK] Validating checksum... 100%</div>
      <div class="term-line" id="l2">[OK] Overflow de memoria corregido.</div>
      <div class="term-line" id="l3">[OK] Acceso concedido al núcleo del sistema.</div>
      <div class="term-line narrative" id="l4">-- NO MESSAGE --</div>
      <div class="term-line" id="l5" style="margin-top: 2rem; text-align: center;">
          <a href="{{ route('prueba4') }}" class="btn" style="text-decoration:none; display:inline-block;">PASAR A LA SIGUIENTE PRUEBA →</a>
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
        verificarChecksum();
    }
});

function verificarChecksum() {
    if (isProcessing) return;
    const value = input.value.trim();

    if (value === "") {
        showError("Introduce un valor numérico válido.");
        return;
    }

    isProcessing = true;
    input.disabled = true;
    btn.disabled = true;
    feedback.textContent = "Verificando firmas locales...";
    feedback.className = "feedback";

    fetch('/prueba3', {
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
            }, 1000);
        } else {
            failedAttempts++;
            showError("[ERROR] Checksum inválido o corrupto.");

            if (failedAttempts >= 5) {
                document.getElementById('hint-box').style.display = 'block';
            }
        }
    })
    .catch(error => {
        showError("Error de conexión con el host.");
    });
}

function showError(msg) {
    input.classList.add('wrong');
    feedback.textContent = msg;
    feedback.className = "feedback err";

    setTimeout(() => {
        input.classList.remove('wrong');
        input.value = '';
        input.disabled = false;
        btn.disabled = false;
        feedback.textContent = "";
        isProcessing = false;
        input.focus();
    }, 1200);
}

function triggerWinSequence(mensajeNarrativo) {
    document.getElementById('screen-hacking').classList.remove('active');
    document.getElementById('screen-win').classList.add('active');

    // Inyectamos el texto final desde backend
    document.getElementById('l4').innerText = mensajeNarrativo;

    // Secuencia de terminal delay
    const lines = [
        { id: 'l1', delay: 500 },
        { id: 'l2', delay: 1500 },
        { id: 'l3', delay: 2200 },
        { id: 'l4', delay: 3500 },
        { id: 'l5', delay: 4500 }
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
