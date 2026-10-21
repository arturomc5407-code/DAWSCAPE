<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>QR Códice Oculto | DAWSCAPE</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');

  :root {
    --green: #00ff88;
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
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.5rem;
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

  .qr-card {
    max-width: 560px;
    width: 100%;
    border: 1px solid var(--border);
    background: var(--panel);
    padding: clamp(1.8rem, 5vw, 2.5rem);
    position: relative;
    animation: fadeIn 0.5s ease;
  }

  .qr-card::before {
    content: '// QR ESCANEADO — MÓDULO CÓDICE OCULTO';
    position: absolute;
    top: -0.7rem; left: 1rem;
    background: var(--bg);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.65rem;
    letter-spacing: 0.2em;
  }

  .scan-icon { font-size: 3rem; text-align: center; margin-bottom: 1.2rem; }

  .scan-title {
    font-family: 'Orbitron', sans-serif;
    font-size: clamp(1rem, 3.5vw, 1.5rem);
    color: var(--green);
    letter-spacing: 0.15em;
    text-align: center;
    margin-bottom: 0.3rem;
  }

  .scan-subtitle {
    font-size: 0.75rem;
    color: #007744;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    text-align: center;
    margin-bottom: 2rem;
  }

  .riddle-section {
    border: 1px solid #00ff8820;
    padding: 1.4rem;
    margin-bottom: 1.8rem;
    position: relative;
  }

  .riddle-section::before {
    content: '// ACERTIJO';
    position: absolute;
    top: -0.65rem; left: 1rem;
    background: var(--panel);
    padding: 0 0.5rem;
    color: var(--green);
    font-size: 0.65rem;
    letter-spacing: 0.2em;
  }

  .riddle-instruction {
    font-size: 0.8rem;
    color: #00aa55;
    margin-bottom: 1rem;
    line-height: 1.5;
  }

  .riddle-text {
    font-size: clamp(0.92rem, 3.5vw, 1.05rem);
    line-height: 1.9;
    color: #aaffcc;
    white-space: pre-line;
  }

  .btn-back {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.85rem;
    letter-spacing: 0.2em;
    padding: 1rem 2rem;
    border: 2px solid var(--green);
    background: transparent;
    color: var(--green);
    cursor: pointer;
    text-transform: uppercase;
    transition: all 0.25s;
    position: relative;
    overflow: hidden;
    width: 100%;
    text-align: center;
    min-height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
    -webkit-tap-highlight-color: transparent;
  }

  .btn-back::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: var(--green);
    transition: left 0.3s;
    z-index: -1;
  }

  .btn-back:hover::before { left: 0; }
  .btn-back:hover { color: var(--bg); }

  @keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body>

<div class="qr-card">
  <div class="scan-icon">📱</div>
  <div class="scan-title">QR DETECTADO</div>
  <div class="scan-subtitle">// Ha escaneado un código QR físico</div>

  <div class="riddle-section">
    <div class="riddle-instruction">
      Resuelve el acertijo — La respuesta a este acertijo es <strong style="color:var(--green)">uno de los números que necesitas para el candado.</strong>
    </div>
    <div class="riddle-text">{{ $acertijo }}</div>
  </div>

  <a href="{{ route('prueba7') }}" class="btn-back">← VOLVER AL CANDADO</a>
</div>

</body>
</html>
