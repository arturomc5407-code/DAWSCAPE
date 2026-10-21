<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>PROTOCOLO ESPEJO | DAWSCAPE</title>
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

  .screen {
      display: none;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      padding-top: 5rem;
      padding-bottom: 2rem;
      min-height: 100vh;
      animation: fadeIn 1s ease;
  }
  .screen.active { display: flex; }

  .logo {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(1.8rem, 5vw, 4rem);
    font-weight: 900;
    color: var(--green);
    text-shadow: 0 0 20px var(--green), 0 0 60px #00ff8840;
    letter-spacing: 0.1em;
    margin-bottom: 2rem;
    text-align: center;
    animation: pulse-glow 2.5s ease-in-out infinite;
  }

  /* MEJORADO: Intro text con mejor contraste */
  .intro-text {
    width: 100%;
    max-width: 600px;
    line-height: 1.8;
    color: #c8ffdf;
    border: 1px solid var(--border);
    padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1.2rem, 4vw, 2rem);
    background: var(--panel);
    position: relative;
    text-align: center;
    font-size: clamp(1rem, 4vw, 1.1rem);
    text-shadow: 0 0 5px rgba(200, 255, 223, 0.1);
  }

  .intro-text::before {
    content: '// ALERTA DE SISTEMA';
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

  .icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      display: block;
      animation: float 3s ease-in-out infinite;
  }

  /* ANIMATIONS */
  @keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); text-shadow: 0 10px 20px rgba(0,255,136,0.3); }
  }
  @keyframes pulse-glow {
    0%, 100% { text-shadow: 0 0 20px var(--green), 0 0 50px #00ff8860; }
    50% { text-shadow: 0 0 40px var(--green), 0 0 90px #00ff8840; }
  }

  /* MOBILE */
  @media (max-width: 600px) {
    .screen { padding-left: 1rem; padding-right: 1rem; padding-top: 5.5rem; }
    .game-header { padding: 0.6rem 1rem; }
    .team-info { font-size: 0.7rem; }
    .header-timer { font-size: 1.1rem; }
    .intro-text { font-size: 0.95rem !important; padding: 1.2rem 1rem; }
    .logo { font-size: clamp(1.5rem, 7vw, 3rem) !important; letter-spacing: 0.05em; }
  }

  /* ===========================
     QR MODAL — Simplificado
  ============================ */
  #qr-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.85);
    z-index: 3000;
  }
  #qr-modal.open { display: flex; }
  .qr-panel {
    width: 100%;
    max-width: 520px;
    background: var(--panel);
    border: 1px solid var(--border);
    box-shadow: 0 0 35px rgba(0,255,136,0.15);
    padding: 1rem;
    position: relative;
  }
  .btn {
    cursor: pointer;
    border: 1px solid var(--border);
    background: rgba(0,255,136,0.08);
    color: var(--text);
    padding: 0.65rem 0.85rem;
    font-family: inherit;
    font-size: 0.95rem;
    transition: transform .08s ease, background .15s ease, border-color .15s ease;
  }
  .btn:hover { background: rgba(0,255,136,0.14); border-color: rgba(0,255,136,0.6); }
  .btn:active { transform: translateY(1px); }
  .btn-danger { background: rgba(255,34,68,0.10); border-color: rgba(255,34,68,0.40); }
  .btn-danger:hover { background: rgba(255,34,68,0.18); border-color: rgba(255,34,68,0.65); }
  #qr-reader {
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    background: #000;
  }
  #qr-reader video { width: 100% !important; height: auto !important; }
  #qr-result {
    margin-top: 0.75rem;
    font-size: 0.9rem;
    color: #b0e0bf;
    line-height: 1.5;
    min-height: 1.4rem;
    text-align: center;
  }
  .btn-scan-main {
    min-height: 52px;
    font-size: 1.1rem;
    font-weight: bold;
    padding: 0.9rem 2rem;
    letter-spacing: 0.08em;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }
  .btn-close-scanner {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    z-index: 10;
    min-width: 36px;
    min-height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
  }

  /* === CHALLENGE & WIN SCREENS === */
  #screen-challenge .intro-text::before { content: '// FIREWALL ALMACÉN'; color: var(--red); }
  .term-list { list-style-type: none; margin: 1.2rem 0; padding-left: 0.5rem; border-left: 2px solid var(--border); }
  .term-list li { margin-bottom: 0.8rem; position: relative; padding-left: 1rem; }
  .term-list li::before { content: '>'; position: absolute; left: 0; color: var(--green); }
  .form-group { margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1rem; align-items: center; width: 100%; }
  .answer-input {
    width: 100%; max-width: 350px; background: #0a0a0a; border: 2px solid var(--border);
    color: var(--green); font-family: 'Share Tech Mono', monospace;
    font-size: max(16px, clamp(1.5rem, 6vw, 2rem)); padding: 0.8rem; border-radius: 8px;
    outline: none; letter-spacing: 0.1em; text-align: center; transition: all 0.2s;
  }
  .answer-input::placeholder { color: #00ff8840; }
  .answer-input:focus { box-shadow: 0 0 20px rgba(0,255,136,0.3); border-color: var(--green); }
  .answer-input.wrong { border-color: var(--red); color: var(--red); animation: shake 0.4s ease; }
  .answer-input.correct { border-color: var(--green); background: #00ff8811; }
  .challenge-btn {
    font-family: 'Orbitron', sans-serif; font-size: 1rem; font-weight: 700;
    letter-spacing: 0.1em; padding: 1rem; border: 2px solid var(--green);
    background: #00ff8811; color: var(--green); border-radius: 8px; cursor: pointer;
    text-transform: uppercase; transition: all 0.25s; width: 100%; max-width: 350px;
    text-decoration: none; text-align: center; display: block;
  }
  .challenge-btn:active { transform: scale(0.98); background: var(--green); color: var(--bg); }
  .challenge-btn:disabled { color: #33aa66; border-color: rgba(51,170,102,0.4); background: rgba(0,255,136,0.05); cursor: not-allowed; pointer-events: none; }
  .feedback { text-align: center; font-size: 0.85rem; min-height: 1.2rem; margin-top: 0.2rem; letter-spacing: 0.05em; width: 100%; }
  .feedback.ok { color: var(--green); }
  .feedback.err { color: var(--red); }
  .hint-box {
    display: none; margin-top: 1rem; padding: 1rem; border: 1px dashed #aa9900;
    color: #ddcc44; font-size: 0.9rem; line-height: 1.4; background: rgba(40,40,0,0.6);
    animation: fadeIn 0.5s ease; border-radius: 6px;
  }
  #term-win {
    width: 100%; max-width: 600px; background: #0a0a0a; border: 1px solid var(--green);
    padding: 1.5rem; font-size: clamp(0.9rem, 4vw, 1rem); line-height: 1.6;
    color: var(--green); box-shadow: inset 0 0 20px rgba(0,255,136,0.1); border-radius: 8px;
  }
  .term-line { opacity: 0; margin-bottom: 0.5rem; }
  .term-line.show { opacity: 1; animation: slideRight 0.3s ease; }
  .term-line.narrative { color: #88ccaa; margin-top: 1.5rem; border-top: 1px dashed var(--border); padding-top: 1.5rem; }
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
  @include('components.game-audio-setup', ['trackKey' => 'prueba5'])
<div class="game-header">
  <div class="team-info">
    <strong>Equipo:</strong> {{ session('nombre_equipo', 'Sin equipo') }}<br>
    <strong>Clase:</strong> {{ session('clase', 'N/A') }}
  </div>
  <div class="header-timer" id="cronometro">--:--</div>
</div>

<div class="screen active" id="screen-scan">
  <div class="logo">PROTOCOLO ESPEJO</div>
  <div class="intro-text">
    <span class="icon">🪞</span>
    <p>El sistema ha sido invertido. El orden natural ha desaparecido. Lo que empieza por A ahora termina en Z.</p>
    <br>
    <p>Dirígete al <strong>OASIS de la P2 (Planta 2)</strong>. El archivo maestro aguarda en formato QR, oculto en uno de los <strong>64 nodos metálicos</strong> alineados.</p>
    <br>
    <p>Si quieres restaurar el núcleo del DAWSCAPE, deberás pensar al revés.</p>
    <p><strong>Busca el muro de almacenamiento, encuentra el QR y descifra el código. El alfabeto ha sido reflejado.</strong></p>
    <br>
    <p style="color: var(--red); font-weight: bold; border-top: 1px dashed var(--border); padding-top: 1rem; margin-top: 1rem;">Solo los más atentos podrán reconstruir la palabra clave.</p>
    <div style="margin-top: 1.25rem; display:flex; justify-content:center;">
      <button id="btn-scan" type="button" class="btn btn-scan-main">📷 ESCANEAR</button>
    </div>
  </div>
</div>

<!-- Modal QR simplificado: solo vídeo + cerrar -->
<div id="qr-modal">
  <div class="qr-panel">
    <button id="btn-close-modal" class="btn btn-danger btn-close-scanner" type="button">✕</button>
    <div id="qr-reader"></div>
    <p id="qr-result"></p>
  </div>
</div>

<!-- CHALLENGE SCREEN (contenido integrado — antes en vista QR separada) -->
<div class="screen" id="screen-challenge">
  <div class="logo">SISTEMA ESPEJO ACTIVADO</div>
  <div class="intro-text">
    <p>El abecedario ha sido invertido para proteger la clave. La correspondencia original ha sido corrompida.</p>
    <p style="margin-top:0.5rem; color:#668877;">Nueva tabla de conversión:</p>
    <p style="color:var(--green); letter-spacing:0.1em; text-align:center; padding: 0.5rem; background: #0a0a0a; border: 1px dashed #00ff8840; margin: 0.5rem 0;">Z = 1 &nbsp; Y = 2 &nbsp; X = 3 &nbsp; ... &nbsp; A = 26</p>
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
    <div style="margin: 1.5rem 0; padding: 1rem; background: #0a0a0a; border: 1px dashed var(--green); text-align: center; font-family: 'Orbitron', sans-serif;">
        <div style="color: #88ccaa; font-size: 0.95rem;">→ Ubicación a visitar: OASIS de la P2 (Planta 2)</div>
        <div style="color: var(--green); font-size: 1.05rem; margin-top: 0.5rem; font-weight:bold;">Dirígete al OASIS de la P2 y descúbrelo por ti mismo</div>
    </div>
    <p style="text-align: center; font-size: 0.9em;"><strong>Introduce la palabra clave reconstruida:</strong></p>
    <div class="form-group">
        <input class="answer-input" id="checksum-input" type="text" placeholder="_______" autocomplete="off" spellcheck="false" autocapitalize="off" maxlength="7">
        <div class="feedback" id="feedback-msg"></div>
        <button class="challenge-btn" id="btn-verificar" type="button">VERIFICAR PALABRA CLAVE →</button>
    </div>
    <div class="hint-box" id="hint-box">
        <strong>[PISTA DEL SISTEMA]</strong><br>Tienes que mirar las taquillas del 1 al 7, arriba hay unos números. Esos números corresponden a letras usando el protocolo espejo, por ejemplo el 1 es la letra Z, el 2 la letra Y, el 3 la letra X y así sucesivamente. Recoge las letras de las taquillas correctas para formar la palabra clave.
    </div>
  </div>
</div>

<!-- WIN SCREEN -->
<div class="screen" id="screen-win">
  <div class="logo">NÚCLEO RESTAURADO</div>
  <div id="term-win">
      <div class="term-line" id="l1">[OK] Reorganizando alfabeto espejo… 100%</div>
      <div class="term-line" id="l2">[OK] Fragmentos recuperados correctamente.</div>
      <div class="term-line" id="l3">[OK] Palabra clave validada.</div>
      <div class="term-line narrative" id="l4">-- NO MESSAGE --</div>
      <div class="term-line" id="l5" style="margin-top: 2rem; text-align: center;">
          <a href="{{ route('prueba6') }}" class="challenge-btn" style="display:inline-block;">PASAR A LA SIGUIENTE PRUEBA →</a>
      </div>
  </div>
</div>

<!-- Librería html5-qrcode (CDN) -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
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
</script>

<script>
// Escaneo QR directo — Prueba 5
(function () {
  const PRUEBA_NUMERO = 5;
  const btnScan  = document.getElementById('btn-scan');
  const modal    = document.getElementById('qr-modal');
  const btnClose = document.getElementById('btn-close-modal');
  const resultEl = document.getElementById('qr-result');
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  let html5QrCode = null;
  let isScanning = false;

  function setResult(msg, isError = false) {
    if (!resultEl) return;
    resultEl.textContent = msg;
    resultEl.style.color = isError ? '#ff8aa0' : '#b0e0bf';
  }

  async function stopScanner() {
    if (!html5QrCode || !isScanning) return;
    try { await html5QrCode.stop(); } catch (e) {}
    isScanning = false;
  }

  function openModal() {
    if (!modal) return;
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  async function closeModal() {
    await stopScanner();
    if (!modal) return;
    modal.classList.remove('open');
    document.body.style.overflow = '';
    setResult('');
  }

  function activarChallenge() {
    if (window.audioManager) window.audioManager.playSFX('correct');
    closeModal();
    document.getElementById('screen-scan').classList.remove('active');
    document.getElementById('screen-challenge').classList.add('active');
  }

  async function validarCodigo(decodedText) {
    setResult('Verificando código...');

    try {
      const resp = await fetch('/validar-codigo-qr', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ codigo: decodedText, prueba: PRUEBA_NUMERO })
      });

      const data = await resp.json().catch(() => ({}));
      if (resp.ok && data && data.success === true) {
        activarChallenge();
        return;
      }

      const msg =
        data?.message ||
        (resp.status === 429
          ? 'Demasiados intentos. Espera un minuto e inténtalo de nuevo.'
          : 'No se pudo validar el código. Inténtalo de nuevo.');
      if (window.audioManager) window.audioManager.playSFX('error');
      setResult(msg, true);
    } catch (err) {
      setResult('Error de red. Revisa tu conexión e inténtalo de nuevo.', true);
    }
  }

  async function startScanner() {
    if (typeof Html5Qrcode === 'undefined') {
      setResult('No se pudo cargar la librería del escáner. Recarga la página.', true);
      return;
    }
    setResult('Abriendo cámara...');
    html5QrCode = new Html5Qrcode('qr-reader');
    const qrSize = Math.min(250, window.innerWidth - 80);
    try {
      await html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: qrSize, height: qrSize } },
        async (decodedText) => {
          await stopScanner();
          await validarCodigo(decodedText);
        },
        () => {}
      );
      isScanning = true;
      setResult('');
    } catch (err) {
      console.error('Error al iniciar cámara:', err);
      setResult('No se pudo acceder a la cámara. Revisa los permisos del navegador.', true);
    }
  }

  btnScan?.addEventListener('click', async () => {
    openModal();
    await startScanner();
  });
  btnClose?.addEventListener('click', closeModal);
  modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
  window.addEventListener('beforeunload', () => { if (isScanning) stopScanner(); });
})();
</script>

<script>
// Challenge logic (contenido integrado de vista QR taquillas)
(function() {
  let challengeFailedAttempts = 0;
  let challengeProcessing = false;
  const challengeInput = document.getElementById('checksum-input');
  const challengeBtn = document.getElementById('btn-verificar');
  const challengeFeedback = document.getElementById('feedback-msg');

  challengeInput?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); challengeInput.blur(); verificarChecksum(); }
  });
  challengeBtn?.addEventListener('click', verificarChecksum);

  function verificarChecksum() {
    if (challengeProcessing) return;
    const value = challengeInput.value.trim();
    if (value === '') { showChallengeError('Introduce una palabra válida.'); return; }

    challengeProcessing = true;
    challengeInput.blur();
    challengeInput.disabled = true;
    challengeBtn.disabled = true;
    challengeFeedback.textContent = 'Analizando bloque de datos...';
    challengeFeedback.className = 'feedback';

    fetch('/prueba5', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ checksum: value })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        if (window.audioManager) window.audioManager.playSFX('correct');
        challengeFeedback.textContent = '[OK] PROTOCOLO ESPEJO RESTAURADO.';
        challengeFeedback.className = 'feedback ok';
        challengeInput.classList.add('correct');
        setTimeout(() => triggerWinSequence(data.mensaje), 800);
      } else {
        challengeFailedAttempts++;
        if (window.audioManager) window.audioManager.playSFX('error');
        showChallengeError('[ERROR] Palabra clave inválida.');
        if (challengeFailedAttempts >= 5) document.getElementById('hint-box').style.display = 'block';
      }
    })
    .catch(() => showChallengeError('Error de comunicación con el núcleo.'));
  }

  function showChallengeError(msg) {
    challengeInput.classList.add('wrong');
    challengeFeedback.textContent = msg;
    challengeFeedback.className = 'feedback err';
    if (navigator.vibrate) navigator.vibrate(200);
    setTimeout(() => {
      challengeInput.classList.remove('wrong');
      challengeInput.value = '';
      challengeInput.disabled = false;
      challengeBtn.disabled = false;
      challengeFeedback.textContent = '';
      challengeProcessing = false;
    }, 1200);
  }

  function triggerWinSequence(mensajeNarrativo) {
    document.getElementById('screen-challenge').classList.remove('active');
    document.getElementById('screen-win').classList.add('active');
    document.getElementById('l4').innerText = mensajeNarrativo;
    [
      { id: 'l1', delay: 400 }, { id: 'l2', delay: 1200 },
      { id: 'l3', delay: 1800 }, { id: 'l4', delay: 2800 },
      { id: 'l5', delay: 3500 }
    ].forEach(line => {
      setTimeout(() => document.getElementById(line.id).classList.add('show'), line.delay);
    });
  }
})();
</script>
</body>
</html>

@include('components.pista-ayuda', ['pruebaNumero' => 5, 'pistaConfig' => $pistaConfig, 'yaUsada' => $yaUsada])
@include('components.live-listener')
