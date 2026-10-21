<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $titulo_prueba }} — DAWSCAPE</title>
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

  .screen { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; padding-top: 5rem; padding-bottom: 2rem; min-height: 100vh;}

  .logo {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(2rem, 5vw, 4rem);
    font-weight: 900;
    color: var(--green);
    text-shadow: 0 0 20px var(--green), 0 0 60px #00ff8840;
    letter-spacing: 0.2em;
    margin-bottom: 1rem;
    text-align: center;
  }
  
  /* MEJORADO: Subtitle con mejor contraste */
  .subtitle {
    font-size: 0.85rem;
    color: #00ff88;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    margin-bottom: 2rem;
    text-shadow: 0 0 10px rgba(0, 255, 136, 0.2);
  }

  /* MEJORADO: Intro text con mejor contraste */
  .intro-text {
    max-width: 580px;
    width: 100%;
    line-height: 1.9;
    color: #c8ffdf;
    border: 1px solid var(--border);
    padding: clamp(1.5rem, 4vw, 2rem) clamp(1.2rem, 4vw, 2rem);
    background: var(--panel);
    position: relative;
    font-size: clamp(0.95rem, 4vw, 1.05rem);
    text-shadow: 0 0 5px rgba(200, 255, 223, 0.1);
  }

  .intro-text::before {
    content: '// PISTA DETECTADA';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.7rem;
    letter-spacing: 0.2em;
  }

  .form-group {
    margin-top: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .answer-input {
    width: 100%;
    background: transparent;
    border: none;
    border-bottom: 2px solid var(--green);
    color: var(--green);
    font-family: 'Share Tech Mono', monospace;
    font-size: 1.3rem;
    padding: 0.8rem 0.5rem;
    outline: none;
    letter-spacing: 0.15em;
    text-align: center;
    text-transform: uppercase;
    transition: border-color 0.2s;
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
    margin-top: 1rem;
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

<div class="screen">
  <div class="logo">{{ strtoupper($titulo_prueba) }}</div>
  <div class="subtitle">// {{ strtoupper($identificador) }}</div>
  <div class="intro-text">
    <p>Agente, has encontrado una nueva ubicación en el mapa. El sistema sugiere que aquí se oculta información vital para continuar.</p>
    <br>
    <p style="color:#445544">Plantilla genérica de prueba. Aquí irá el contenido específico del QR escaneado (formularios, pistas, etc.).</p>
    
    <div class="form-group">
        <input class="answer-input" type="text" placeholder="CÓDIGO_DE_ACCESO" autocomplete="off" spellcheck="false" disabled>
        <button class="btn" style="opacity: 0.5; cursor: not-allowed;">VERIFICAR →</button>
    </div>
  </div>
</div>

<script>
let tiempo = parseInt("{{ $tiempo_transcurrido ?? 0 }}");
const timerDisplay = document.getElementById('cronometro');
const MAX_MINUTOS = 90;

function actualizarTimer() {
  let minutos = parseInt(tiempo / 60, 10);
  let segundos = parseInt(tiempo % 60, 10);

  let m = minutos  < 10 ? "0" + minutos  : minutos;
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
</script>
</body>
</html>
