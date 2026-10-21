<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>DAWSCAPE | Briefing</title>
<link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');

  :root {
    --green: #00ff88;
    --green-rgb: 0, 255, 136;
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
    display: flex;
    align-items: center;
    justify-content: center;
  }

  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,255,136,0.013) 2px, rgba(0,255,136,0.013) 4px);
    pointer-events: none;
    z-index: 1000;
  }

  /* ═══ LAYOUT PRINCIPAL ═══ */
  .sergio-screen {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 2rem 1.5rem;
    position: relative;
    z-index: 10;
    gap: 1.5rem;
    animation: fadeIn 1s ease;
  }

  /* ═══ PERSONAJE ═══ */
  .character-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.6rem;
  }

  .character-frame {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 4px solid var(--green);
    overflow: hidden;
    background: linear-gradient(135deg, #041a0e, #0a2f18);
    box-shadow: 0 0 25px rgba(0,255,136,0.4), 0 8px 25px rgba(0,0,0,0.6);
    position: relative;
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  .character-frame img {
    width: 100%; height: 100%;
    object-fit: cover;
    image-rendering: pixelated;
    display: block;
  }

  .character-frame.talking {
    border-color: #66ffaa;
    box-shadow: 0 0 30px rgba(0,255,136,0.6), 0 8px 25px rgba(0,0,0,0.6);
  }

  .character-name {
    font-family: 'Orbitron', sans-serif;
    font-size: 1rem;
    color: var(--green);
    text-shadow: 0 0 10px rgba(0,255,136,0.5);
    letter-spacing: 0.15em;
    text-transform: uppercase;
  }

  /* ═══ BOCADILLO DE DIÁLOGO ═══ */
  .speech-container {
    width: 90%;
    max-width: 580px;
  }

  .speech-box {
    position: relative;
    background: linear-gradient(180deg, #f8f5e8 0%, #e8e4d4 100%);
    border: 4px solid #2a2a3e;
    border-radius: 14px;
    padding: 1.5rem 1.3rem 1.3rem;
    box-shadow: 0 4px 30px rgba(0,0,0,0.5), inset 0 2px 0 rgba(255,255,255,0.3);
    min-height: 140px;
  }

  /* Punta del bocadillo apuntando arriba */
  .speech-box::before {
    content: '';
    position: absolute;
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
    width: 0; height: 0;
    border-left: 14px solid transparent;
    border-right: 14px solid transparent;
    border-bottom: 14px solid #2a2a3e;
  }
  .speech-box::after {
    content: '';
    position: absolute;
    top: -9px;
    left: 50%;
    transform: translateX(-50%);
    width: 0; height: 0;
    border-left: 11px solid transparent;
    border-right: 11px solid transparent;
    border-bottom: 11px solid #f8f5e8;
  }

  .speech-text {
    font-family: 'Share Tech Mono', monospace;
    font-size: clamp(0.85rem, 2.8vw, 1rem);
    color: #1a1a2e;
    line-height: 1.8;
    min-height: 60px;
    word-wrap: break-word;
  }

  /* Cursor parpadeante */
  .speech-text .speech-cursor {
    display: inline-block;
    width: 8px;
    height: 1.1em;
    background: #1a1a2e;
    margin-left: 2px;
    vertical-align: text-bottom;
    animation: cursor-blink 0.6s steps(2) infinite;
  }
  @keyframes cursor-blink {
    0%   { opacity: 1; }
    50%  { opacity: 0; }
    100% { opacity: 1; }
  }

  /* ═══ BOTÓN ═══ */
  .action-btn-wrapper {
    width: 90%;
    max-width: 400px;
    opacity: 0;
    transform: translateY(15px);
    pointer-events: none;
    transition: opacity 0.6s ease, transform 0.6s ease;
  }

  .action-btn-wrapper.visible {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
  }

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
    text-decoration: none;
    display: block;
    text-align: center;
    min-height: 52px;
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
  .btn:hover { color: var(--bg); box-shadow: 0 0 20px var(--green); }

  /* ═══ PARTÍCULAS ═══ */
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

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes pulse-glow {
    0%, 100% { text-shadow: 0 0 15px var(--green), 0 0 40px #00ff8860; }
    50% { text-shadow: 0 0 30px var(--green), 0 0 70px #00ff8840; }
  }

  /* ═══ MOBILE ═══ */
  @media (max-width: 600px) {
    .sergio-screen { padding: 1.5rem 1rem; }
    .character-frame { width: 110px; height: 110px; }
    .speech-box { padding: 1.2rem 1rem 1rem; min-height: 120px; }
    .speech-text { font-size: 0.82rem; }
    .btn { font-size: 0.85rem; letter-spacing: 0.15em; }
  }
</style>
</head>
<body>

<div class="particles" id="particles"></div>

<div class="sergio-screen">
  <!-- Personaje -->
  <div class="character-area">
    <div class="character-frame" id="charFrame">
      <img src="/images/pistas/sergio.png" alt="Sergio" id="charImg"
           data-img-closed="/images/pistas/sergio.png"
           data-img-open="/images/pistas/sergio1.png">
    </div>
    <div class="character-name">Sergio</div>
  </div>

  <!-- Bocadillo de diálogo -->
  <div class="speech-container">
    <div class="speech-box">
      <div class="speech-text" id="speechText"></div>
    </div>
  </div>

  <!-- Mensaje de redirección (aparece al terminar el texto) -->
  <div class="action-btn-wrapper" id="actionBtnWrapper">
    <div style="text-align:center; margin-bottom:1.2rem; font-family:'Orbitron',sans-serif; font-size:clamp(1rem,3vw,1.3rem); color:var(--green); text-shadow:0 0 15px var(--green); letter-spacing:0.15em; animation: pulse-glow 2s ease-in-out infinite;">
      Dirígete al LAB 115
    </div>
    <a href="{{ route('prueba1') }}" class="btn">ENTRAR A DAWSCAPE →</a>
  </div>
</div>

<script>
// ═══ PARTÍCULAS ═══
(function() {
  var c = document.getElementById('particles');
  for (var i = 0; i < 25; i++) {
    var p = document.createElement('div');
    p.className = 'particle';
    p.style.left = Math.random() * 100 + '%';
    p.style.animationDuration = (10 + Math.random() * 14) + 's';
    p.style.animationDelay = (Math.random() * 12) + 's';
    c.appendChild(p);
  }
})();

// ═══ SISTEMA DE HABLA (copiado de pista-ayuda.blade.php) ═══
(function() {
  var VELOCIDAD_TEXTO = 35; // ms por carácter
  var MOUTH_INTERVAL_MS = 150; // velocidad alternancia boca

  var charFrame = document.getElementById('charFrame');
  var charImg = document.getElementById('charImg');
  var speechText = document.getElementById('speechText');
  var actionBtnWrapper = document.getElementById('actionBtnWrapper');

  var imgClosed = charImg.getAttribute('data-img-closed');
  var imgOpen = charImg.getAttribute('data-img-open');

  var speakingAudio = null;
  var mouthAnimInterval = null;
  var typewriterInterval = null;
  var mouthOpen = false;

  // Precargar imagen de boca abierta
  var preload = new Image();
  preload.src = imgOpen;

  // ── Animación de boca ──
  function iniciarAnimBoca() {
    charFrame.classList.add('talking');
    mouthOpen = false;
    mouthAnimInterval = setInterval(function() {
      mouthOpen = !mouthOpen;
      charImg.src = mouthOpen ? imgOpen : imgClosed;
    }, MOUTH_INTERVAL_MS);
  }

  function detenerAnimBoca() {
    charFrame.classList.remove('talking');
    if (mouthAnimInterval) {
      clearInterval(mouthAnimInterval);
      mouthAnimInterval = null;
    }
    mouthOpen = false;
    charImg.src = imgClosed;
  }

  // ── Sonido de habla ──
  var _speakingAudioUrl = ((window.AUDIO_BASE_URL || '/sonido').replace(/\/$/, '')) + '/hablando-pista.mp3';

  function iniciarSonidoHabla() {
    try {
      if (speakingAudio) { speakingAudio.pause(); }
      speakingAudio = new Audio(_speakingAudioUrl);
      speakingAudio.loop = true;
      speakingAudio.volume = 0.85;
      speakingAudio.play().catch(function() {});
    } catch(e) {}
  }

  function detenerSonidoHabla() {
    if (speakingAudio) {
      speakingAudio.pause();
      speakingAudio.currentTime = 0;
      speakingAudio = null;
    }
  }

  // ── Typewriter ──
  function typewriter(texto, callback) {
    if (typewriterInterval) { clearInterval(typewriterInterval); }
    speechText.innerHTML = '';
    var i = 0;

    iniciarSonidoHabla();
    iniciarAnimBoca();

    var cursorSpan = document.createElement('span');
    cursorSpan.className = 'speech-cursor';

    typewriterInterval = setInterval(function() {
      if (i < texto.length) {
        speechText.textContent = texto.substring(0, i + 1);
        speechText.appendChild(cursorSpan);
        i++;
      } else {
        clearInterval(typewriterInterval);
        typewriterInterval = null;
        detenerSonidoHabla();
        detenerAnimBoca();
        setTimeout(function() {
          if (cursorSpan.parentNode) cursorSpan.remove();
        }, 600);
        if (callback) callback();
      }
    }, VELOCIDAD_TEXTO);
  }

  // ── Texto de Sergio ──
  var textoSergio = "Hey, mira que rico te escapas... Tenéis que resolver 9 pruebas en orden. Algunas son acertijos en pantalla, otras os van a mandar por el instituto a buscar pistas escondidas, así que estad muy atentos. Cada pista extra que pidáis suma 8 minutos a vuestro tiempo, así que pensad antes de usarlas. Gana el equipo que termine en menos tiempo.";

  // Iniciar con un pequeño delay para que la animación de entrada se vea
  setTimeout(function() {
    typewriter(textoSergio, function() {
      // Mostrar botón al terminar
      actionBtnWrapper.classList.add('visible');
    });
  }, 800);
})();
</script>
</body>
</html>
