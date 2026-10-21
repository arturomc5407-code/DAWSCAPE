<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ERROR — DAWSCAPE</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');

  :root {
    --green: #00ff88;
    --red: #ff2244;
    --bg: #020c06;
    --panel: #041a0e;
    --border: #00ff8840;
    --text: #c8ffdf;
    --border-err: #ff224440;
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
    background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,34,68,0.02) 2px, rgba(255,34,68,0.02) 4px);
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
    border-bottom: 1px solid var(--border-err);
    z-index: 100;
  }

  .team-info {
    font-size: 0.75rem;
    color: #cc8888;
    line-height: 1.6;
  }

  .team-info strong { color: var(--red); }

  .header-timer {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.3rem;
    color: var(--red);
    text-shadow: 0 0 10px var(--red);
    letter-spacing: 0.1em;
  }

  .screen { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; padding-top: 5rem; min-height: 100vh; }

  .logo {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(2rem, 5vw, 4rem);
    font-weight: 900;
    color: var(--red);
    text-shadow: 0 0 20px var(--red), 0 0 60px #ff224440;
    letter-spacing: 0.2em;
    margin-bottom: 2rem;
  }

  .intro-text {
    max-width: 580px;
    line-height: 1.9;
    color: #ffcccc;
    border: 1px solid var(--border-err);
    padding: 1.8rem 2rem;
    background: #1a0404;
    position: relative;
    text-align: center;
    font-size: 1.1rem;
    margin-bottom: 2rem;
  }

  .intro-text::before {
    content: '// ACCESO DENEGADO';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--red);
    font-size: 0.7rem;
    letter-spacing: 0.2em;
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

<div class="screen">
  <div class="logo">ERROR 403</div>
  <div class="intro-text">
    {{ $mensaje }}
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
