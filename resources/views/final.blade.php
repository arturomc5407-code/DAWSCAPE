<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VICTORIA | DAWSCAPE</title>
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

      .screen {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 1.5rem;
        position: relative;
        z-index: 10;
        animation: fadeIn 1s ease;
      }

      .victory-box {
        width: 100%;
        max-width: 680px;
        border: 1px solid var(--border);
        background: var(--panel);
        padding: clamp(1.5rem, 5vw, 3rem) clamp(1rem, 4vw, 3rem);
        position: relative;
        text-align: center;
        box-shadow: 0 0 40px rgba(0, 255, 136, 0.1);
      }

      .victory-box::before {
        content: '// REPORTE DEL SISTEMA';
        position: absolute;
        top: -0.7rem; left: 1rem;
        background: var(--bg);
        padding: 0 0.5rem;
        color: var(--green);
        font-size: 0.7rem;
        letter-spacing: 0.2em;
      }

      .victory-box.timeout {
        border-color: var(--red);
        box-shadow: 0 0 40px rgba(255, 34, 68, 0.15);
      }
      .victory-box.timeout::before {
        color: var(--red);
      }

      .title {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(2rem, 5vw, 3.5rem);
        font-weight: 900;
        letter-spacing: 0.1em;
        margin-bottom: 1.5rem;
      }

      .title.success {
        color: var(--green);
        text-shadow: 0 0 20px var(--green);
        animation: pulse-glow 2s infinite;
      }

      .title.error {
        color: var(--red);
        text-shadow: 0 0 20px var(--red);
        animation: glitch-blink 2s infinite;
      }

      /* MEJORADO: Subtitle text con mejor contraste */
      .subtitle-txt {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #b0e0bf;
        margin-bottom: 2rem;
        text-shadow: 0 0 6px rgba(200, 255, 223, 0.15);
      }
      .victory-box.timeout .subtitle-txt {
        color: #ff9999;
        text-shadow: 0 0 6px rgba(255, 153, 153, 0.2);
      }

      .highlight { color: var(--green); font-weight: bold; }
      .victory-box.timeout .highlight { color: var(--red); }

      .time-display {
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(2rem, 10vw, 3.5rem);
        color: var(--green);
        text-shadow: 0 0 20px var(--green);
        margin: 1.5rem 0;
        letter-spacing: 0.1em;
      }

      .ranking-panel {
        text-align: left;
        background: rgba(0, 0, 0, 0.4);
        border: 1px dotted var(--border);
        padding: clamp(1rem, 4vw, 1.5rem);
        margin-top: 1.5rem;
      }

      /* MEJORADO: Ranking panel header con mejor contraste */
      .ranking-panel h4 {
        color: #00ff88;
        font-size: 0.85rem;
        letter-spacing: 0.2em;
        margin-bottom: 1rem;
        text-transform: uppercase;
        text-shadow: 0 0 8px rgba(0, 255, 136, 0.2);
      }

      .breakdown-panel {
          margin-top: 1rem;
          margin-bottom: 2rem;
          background: rgba(0, 255, 136, 0.05);
          border-left: 3px solid var(--green);
          padding: 1.5rem;
          text-align: left;
      }

      .breakdown-panel h4 {
        color: var(--green);
        font-size: 0.85rem;
        letter-spacing: 0.2em;
        margin-bottom: 1rem;
        text-transform: uppercase;
      }

      /* MEJORADO: Breakdown list con mejor contraste */
      .breakdown-list {
          list-style: none;
          padding: 0;
          font-size: 0.95rem;
          line-height: 2;
          color: #b0e0bf;
          text-shadow: 0 0 5px rgba(200, 255, 223, 0.1);
      }

      .breakdown-list li {
          display: flex;
          justify-content: space-between;
          border-bottom: 1px solid rgba(0, 255, 136, 0.1);
          padding-bottom: 0.3rem;
          margin-bottom: 0.3rem;
      }

      /* MEJORADO: Ranking list con mejor contraste */
      .ranking-list {
        list-style-type: decimal;
        padding-left: 1.5rem;
        color: #88ccaa;
        line-height: 1.8;
        text-shadow: 0 0 4px rgba(136, 204, 170, 0.15);
      }

      .ranking-list li { margin-bottom: 0.5rem; }
      .ranking-list li.current-team {
        color: var(--green);
        font-weight: bold;
        text-shadow: 0 0 5px var(--green);
      }

      /* DIPLOMA FINAL */
      /* =================== DIPLOMA REDESIGN =================== */
      .diploma-section {
        margin-top: 1.5rem;
        padding: clamp(0.4rem, 2vw, 0.75rem);
        width: 100%;
      }
      .diploma-card {
        position: relative;
        background: #050505;
        border: 3px solid #00ff88;
        box-shadow: 0 0 40px rgba(0,255,136,0.25), 0 0 80px rgba(0,255,136,0.08), inset 0 0 50px rgba(0,255,136,0.02);
        padding: clamp(1.5rem, 4vw, 3rem) clamp(1.5rem, 4vw, 3.5rem);
        overflow: hidden;
        font-family: 'Share Tech Mono', monospace;
        color: #c8ffdf;
      }
      .diploma-card::before {
        content: '';
        position: absolute;
        top: 14px; left: 14px; right: 14px; bottom: 14px;
        border: 1px solid rgba(0,255,136,0.25);
        pointer-events: none;
        z-index: 0;
      }
      .diplo-scanlines {
        position: absolute;
        inset: 0;
        background-image: repeating-linear-gradient(0deg, transparent, transparent 3px, rgba(0,0,0,0.18) 3px, rgba(0,0,0,0.18) 4px);
        pointer-events: none;
        z-index: 1;
      }
      .diplo-watermark {
        position: absolute;
        inset: 0;
        overflow: hidden;
        font-size: clamp(2rem, 5vw, 4rem);
        font-family: 'Share Tech Mono', monospace;
        color: rgba(0,255,136,0.04);
        line-height: 1.4;
        word-break: break-all;
        pointer-events: none;
        z-index: 0;
        padding: 1rem;
        user-select: none;
      }
      .diplo-corner {
        position: absolute;
        font-family: 'Share Tech Mono', monospace;
        font-size: clamp(1.2rem, 3.5vw, 2.2rem);
        color: #00ff88;
        text-shadow: 0 0 12px rgba(0,255,136,0.9);
        z-index: 3;
        line-height: 1;
      }
      .diplo-tl { top: 14px; left: 16px; }
      .diplo-tr { top: 14px; right: 16px; }
      .diplo-bl { bottom: 14px; left: 16px; }
      .diplo-br { bottom: 14px; right: 16px; }
      .diplo-content { position: relative; z-index: 2; text-align: center; }
      .diplo-system-tag {
        font-size: clamp(0.5rem, 1.4vw, 0.75rem);
        color: rgba(0,255,136,0.65);
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin-bottom: 0.4rem;
      }
      @keyframes diplomaGlitch {
        0%,89%,100% { text-shadow: 0 0 20px rgba(0,255,136,0.5); transform: translate(0,0); }
        90% { transform: translate(-3px,0); text-shadow: -3px 0 0 rgba(255,0,60,0.7), 3px 0 0 rgba(0,240,255,0.7); }
        92% { transform: translate(3px,0); }
        94% { transform: translate(-1px,0); }
        96% { transform: translate(0,0); }
      }
      .diplo-title {
        font-size: clamp(1.3rem, 4.5vw, 2.8rem);
        font-weight: 900;
        color: #ffffff;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        text-shadow: 0 0 20px rgba(0,255,136,0.5);
        animation: diplomaGlitch 7s infinite;
        margin: 0.2rem 0;
        font-family: 'Share Tech Mono', monospace;
      }
      .diplo-sep {
        color: rgba(0,255,136,0.45);
        font-size: clamp(0.45rem, 1.2vw, 0.75rem);
        margin: 0.45rem 0;
        overflow: hidden;
        white-space: nowrap;
        letter-spacing: -0.02em;
      }
      .diplo-certify-text { font-size: clamp(0.7rem, 1.8vw, 0.95rem); color: #888; margin: 0.2rem 0; }
      @keyframes teamNamePulse {
        0%,100% { text-shadow: 0 0 10px rgba(0,255,136,0.7), 0 0 30px rgba(0,255,136,0.3); }
        50%      { text-shadow: 0 0 20px #00ff88, 0 0 60px rgba(0,255,136,0.5), 0 0 100px rgba(0,255,136,0.15); }
      }
      .diplo-team-name {
        font-size: clamp(1.4rem, 4.5vw, 2.8rem);
        font-weight: 700;
        color: #00ff88;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        animation: teamNamePulse 2.5s ease-in-out infinite;
        margin: 0.4rem 0 0.2rem;
        font-family: 'Share Tech Mono', monospace;
        word-break: break-word;
      }
      .diplo-award-label {
        font-size: clamp(0.55rem, 1.3vw, 0.78rem);
        color: rgba(0,255,136,0.55);
        letter-spacing: 0.18em;
        margin: 0.25rem 0;
      }
      .diploma-award-emoji { font-size: clamp(1.8rem, 5vw, 3.5rem); margin: 0.15rem 0; }
      .diploma-award-title {
        font-size: clamp(0.9rem, 2.5vw, 1.4rem);
        font-weight: 700;
        color: #ffd700;
        text-shadow: 0 0 15px rgba(255,215,0,0.6);
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 0.2rem;
        font-family: 'Share Tech Mono', monospace;
      }
      .diplo-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.35rem 1.5rem;
        margin: 0.4rem auto;
        max-width: 680px;
        text-align: left;
      }
      .diplo-stat {
        font-size: clamp(0.6rem, 1.6vw, 0.88rem);
        color: #c8ffdf;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
      }
      .diplo-stat-label { color: #777; }
      .diplo-stat-val { color: #00ff88; font-weight: bold; }
      .diplo-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 0.5rem;
        gap: 0.75rem;
      }
      .diplo-footer-left { text-align: left; flex: 1; min-width: 0; }
      .diplo-cert-code {
        font-size: clamp(0.5rem, 1.3vw, 0.72rem);
        color: rgba(0,255,136,0.45);
        letter-spacing: 0.12em;
        margin-bottom: 0.2rem;
      }
      .diplo-footer-text { font-size: clamp(0.45rem, 1.1vw, 0.65rem); color: #555; }
      .diplo-seal {
        width: clamp(70px, 10vw, 130px);
        height: clamp(70px, 10vw, 130px);
        flex-shrink: 0;
        filter: drop-shadow(0 0 8px rgba(0,255,136,0.5));
      }
      .diploma-actions {
        margin-top: 0.75rem;
        display: flex;
        justify-content: center;
        width: 100%;
      }
      .diploma-download-btn {
        display: inline-block;
        font-family: 'Orbitron', sans-serif;
        font-size: clamp(0.6rem, 2vw, 0.75rem);
        letter-spacing: 0.18em;
        padding: 0.9rem 1.6rem;
        border: 1px solid var(--green);
        color: var(--green);
        background: transparent;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.2s;
        max-width: 100%;
        text-align: center;
        -webkit-tap-highlight-color: transparent;
        min-height: 48px;
      }
      .diploma-download-btn:hover {
        background: var(--green);
        color: #00110a;
        box-shadow: 0 0 14px rgba(0,255,136,0.4);
      }
      /* Print styles removed — diploma downloads as PNG via html2canvas */

      .btn {
        display: inline-block;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.9rem;
        letter-spacing: 0.2em;
        padding: 1.2rem 2.5rem;
        border: 2px solid var(--green);
        background: transparent;
        color: var(--green);
        text-decoration: none;
        cursor: pointer;
        text-transform: uppercase;
        transition: all 0.25s;
        position: relative;
        overflow: hidden;
        margin-top: 2rem;
        font-weight: 700;
        width: 100%;
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

      @keyframes pulse-glow {
        0%, 100% { text-shadow: 0 0 20px var(--green); }
        50% { text-shadow: 0 0 40px var(--green), 0 0 60px #00ff8840; }
      }

      @keyframes glitch-blink {
        0%, 100% { opacity: 1; text-shadow: 0 0 20px var(--red); }
        50% { opacity: 0.8; text-shadow: 0 0 40px var(--red); }
      }

      @keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

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

      /* MOBILE */
      .btn { min-height: 52px; -webkit-tap-highlight-color: transparent; }
      @media (max-width: 600px) {
        .screen { padding: 0.75rem; }
        .victory-box { padding: 1rem 0.75rem; }
        .victory-box::before { font-size: 0.6rem; left: 0.5rem; }
        .title { font-size: clamp(1.5rem, 7vw, 2.5rem); margin-bottom: 1rem; }
        .breakdown-list { font-size: 0.85rem; }
        .breakdown-panel { padding: 1rem; }
        .subtitle-txt { font-size: 0.95rem; line-height: 1.6; }
        .ranking-list { font-size: 0.85rem; padding-left: 1.2rem; }
        .ranking-list li { flex-wrap: wrap; gap: 0.2rem; }
        .time-display { font-size: clamp(1.5rem, 8vw, 2.5rem); }
        .diploma-section { padding: 0.4rem; }
        .diplo-stats { grid-template-columns: 1fr; gap: 0.3rem; max-width: 100%; }
        .diplo-stat { white-space: normal; }
        .diplo-team-name { font-size: 1.4rem; }
        .diploma-award-emoji { font-size: 2rem; }
        .diplo-footer { flex-direction: column; align-items: center; text-align: center; }
        .diplo-footer-left { text-align: center; }
        .diplo-seal { width: 90px; height: 90px; }
        .diploma-download-btn { padding: 0.75rem 1rem; width: 100%; }
        .btn { padding: 1rem 1.5rem; font-size: 0.8rem; }
      }

      @media (max-width: 380px) {
        .screen { padding: 0.5rem; }
        .victory-box { padding: 0.75rem 0.5rem; }
        .title { font-size: 1.4rem; }
        .subtitle-txt { font-size: 0.85rem; }
        .ranking-list { font-size: 0.8rem; }
      }
    </style>
</head>
<body>

@include('components.game-audio-setup', ['trackKey' => 'final'])

<div class="particles" id="particles"></div>

<div class="screen">
  @if($partida->fase_actual === 'FINALIZADO_TIMEOUT' || session('timeout'))
    <div class="victory-box timeout">
      <div style="font-size:3rem; margin-bottom:1rem">🚫</div>
      <h1 class="title error">TIEMPO AGOTADO</h1>

      <div class="subtitle-txt">
        Misión fallida, equipo <span class="highlight">{{ $partida->nombre_equipo }}</span> ({{ $partida->clase }}).<br><br>
        El sistema de seguridad ha bloqueado la salida y se acabó vuestro tiempo.
      </div>

      <div class="ranking-panel" style="border-color: #ffaa0040;">
        <h4 style="color:#ffaa00">>> Progreso antes del cierre_</h4>
        <div style="color:#ffddaa; font-size:1.1rem; margin-top:0.5rem">
          @if(session()->has('prueba1mario_superada'))
            Prueba 1 Mario completada (fallo en validación final).
          @else
            No habéis conseguido escapar a tiempo.
          @endif
        </div>
      </div>
  @else
    <div class="victory-box">
      <div style="font-size:3.5rem; margin-bottom:1rem">🔓</div>
      <h1 class="title success">SISTEMA HACKEADO</h1>

      <div class="subtitle-txt">
        Enhorabuena, equipo <span class="highlight">{{ $partida->nombre_equipo }}</span> ({{ $partida->clase }}).<br><br>
        Habéis conseguido escapar del edificio a tiempo cruzando el Firewall.
      </div>

      <div style="color:#007744; font-size:0.8rem; letter-spacing:0.2em; text-transform:uppercase">// TIEMPO TOTAL DE OPERACIÓN</div>
      <div class="time-display">
        {{ floor($partida->tiempo_segundos / 60) }}m {{ $partida->tiempo_segundos % 60 }}s
      </div>
      @if($partida->penalizacion_segundos > 0)
        <div style="color: #f59e0b; font-size: 0.9rem; margin-top: -10px; margin-bottom: 20px;">
          (Incluye {{ floor($partida->penalizacion_segundos / 60) }}m de penalización por pistas)
        </div>
      @endif

      @if(isset($hitos) && count($hitos) > 0)
          <div class="breakdown-panel">
            <h4>>> INFORME DE RENDIMIENTO (POR FASE)_</h4>
            <ul class="breakdown-list">
              @foreach($hitos as $hito)
                  <li>
                      <span>{{ $hito['fase'] }}</span>
                      <span style="color:var(--green); font-family: 'Orbitron', sans-serif;">
                          {{ floor($hito['segundos_invertidos'] / 60) }}m {{ $hito['segundos_invertidos'] % 60 }}s
                      </span>
                  </li>
              @endforeach
            </ul>
          </div>
      @endif
  @endif

      <div class="ranking-panel">
        <h4>>> TOP 3 EQUIPOS_</h4>
        @if($top3->count() > 0)
          <ol class="ranking-list">
              @foreach($top3 as $equipo)
                  <li class="{{ $equipo->email === $partida->email ? 'current-team' : '' }}" style="display:flex; justify-content:space-between; align-items:baseline; gap:0.5rem">
                      <span>{{ $equipo->nombre_equipo }} ({{ $equipo->clase }})</span> <span style="font-family:'Orbitron', sans-serif; white-space:nowrap">[{{ floor($equipo->tiempo_segundos / 60) }}m {{ $equipo->tiempo_segundos % 60 }}s]</span>
                  </li>
              @endforeach
          </ol>
        @else
            <p style="color:#445544; font-size:0.9rem">Aún no hay datos de ranking.</p>
        @endif
      </div>

      @if($partida->fase_actual === 'FINALIZADO')
      <div class="diploma-section" id="diploma-section">
        <div class="diploma-card">
          <!-- Corner decorations -->
          <div class="diplo-corner diplo-tl">╔</div>
          <div class="diplo-corner diplo-tr">╗</div>
          <div class="diplo-corner diplo-bl">╚</div>
          <div class="diplo-corner diplo-br">╝</div>
          <!-- Scanlines overlay -->
          <div class="diplo-scanlines"></div>
          <!-- Watermark (populated by JS) -->
          <div class="diplo-watermark" id="diplo-watermark"></div>

          <div class="diplo-content">
            <div class="diplo-system-tag">[ DAWSCAPE // SISTEMA DE CERTIFICACIÓN DIGITAL ]</div>
            <h2 class="diplo-title">CERTIFICADO DE AVENTURA</h2>
            <div class="diplo-sep">════════════════════════════════════════════════════════════════════</div>

            <p class="diplo-certify-text">Este diploma certifica que el equipo</p>
            <div class="diplo-team-name">{{ $partida->nombre_equipo }}</div>
            <p class="diplo-certify-text">ha completado con éxito el desafío DAWSCAPE</p>

            <div class="diplo-sep">════════════════════════════════════════════════════════════════════</div>
            <div class="diplo-award-label">— ◆ PREMIO OBTENIDO ◆ —</div>
            <div class="diploma-award-emoji" id="diploma-award-emoji">🏆</div>
            <div class="diploma-award-title" id="diploma-award-title">Cargando premio...</div>

            <div class="diplo-sep">════════════════════════════════════════════════════════════════════</div>
            <div class="diplo-stats">
              <div class="diplo-stat">
                <span>⏱</span>
                <span class="diplo-stat-label">Tiempo total:</span>
                <span class="diplo-stat-val" id="diplo-time">--</span>
              </div>
              <div class="diplo-stat">
                <span>📅</span>
                <span class="diplo-stat-label">Fecha del reto:</span>
                <span class="diplo-stat-val" id="diplo-date">--</span>
              </div>
              <div class="diplo-stat">
                <span>🏆</span>
                <span class="diplo-stat-label">Posición ranking:</span>
                <span class="diplo-stat-val" id="diplo-pos">#--</span>
              </div>
              <div class="diplo-stat">
                <span>🔓</span>
                <span class="diplo-stat-label">Retos resueltos:</span>
                <span class="diplo-stat-val" id="diplo-retos">-/9</span>
              </div>
            </div>

            <div class="diplo-sep">════════════════════════════════════════════════════════════════════</div>
            <div class="diplo-footer">
              <div class="diplo-footer-left">
                <div class="diplo-cert-code" id="diplo-cert">[ CERT-00000000 ]</div>
                <div class="diplo-footer-text">DAWSCAPE — Escape Room Digital &middot; XTART &middot; 2026</div>
              </div>
              <div class="diplo-seal">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" overflow="visible">
                  <defs><path id="diplo-circle-path" d="M 100,12 A 88,88 0 1 1 99.9,12"/></defs>
                  <circle cx="100" cy="100" r="90" fill="none" stroke="#00ff88" stroke-width="3"/>
                  <circle cx="100" cy="100" r="74" fill="none" stroke="rgba(0,255,136,0.4)" stroke-width="1.5"/>
                  <circle cx="100" cy="100" r="70" fill="rgba(0,255,136,0.06)"/>
                  <text fill="#00ff88" font-family="Share Tech Mono,monospace" font-size="12.5">
                    <textPath href="#diplo-circle-path"> · DAWSCAPE · XTART · 2026 · VERIFIED ·</textPath>
                  </text>
                  <text x="100" y="94" text-anchor="middle" fill="#00ff88" font-family="Share Tech Mono,monospace" font-size="19" font-weight="bold">DAWS</text>
                  <text x="100" y="116" text-anchor="middle" fill="#00ff88" font-family="Share Tech Mono,monospace" font-size="19" font-weight="bold">CAPE</text>
                </svg>
              </div>
            </div>
          </div>
        </div>
        <div class="diploma-actions">
          <button type="button" class="diploma-download-btn" id="download-diploma">
            DESCARGAR DIPLOMA
          </button>
        </div>
      </div>
      @endif

      <a href="/" class="btn">FINALIZAR CONEXIÓN →</a>
    </div>
</div>

<script src="{{ asset('js/html2canvas.min.js') }}"></script>
<script>
// Partículas
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

// =========================
// SISTEMA DE DIPLOMA FINAL
// =========================
(function() {
  const section = document.getElementById('diploma-section');
  if (!section) return; // solo cuando el equipo ha finalizado

  const stats = {
    teamName: @json($partida->nombre_equipo),
    totalTime: {{ (int)($partida->tiempo_segundos ?? 0) }},
    timePerChallenge: @json(isset($hitos) ? collect($hitos)->pluck('segundos_invertidos')->values() : []),
    hintsUsed: 0,
    maxHints: 10,
    firstTrySuccessCount: 0,
    totalAttempts: 0,
    position: 0,
    totalChallenges: {{ isset($hitos) ? count($hitos) : 0 }},
  };

  // Helpers
  const minutes = (s) => s / 60;
  const ratio = (n, d) => (d === 0 ? 0 : n / d);
  const fastestChallengeTime = (st) =>
    st.timePerChallenge.length ? Math.min.apply(null, st.timePerChallenge) : Infinity;

  // Definición de premios (subset principal)
  const AWARDS = [
    {
      id: 'first_finish',
      label: 'Primer equipo en terminar',
      emoji: '🏆',
      weight: 1000,
      score: (s) => (s.position === 1 ? 100 : 0),
    },
    {
      id: 'velocista_digital',
      label: 'Velocista Digital',
      emoji: '⚡',
      weight: 850,
      score: (s) => {
        const m = minutes(s.totalTime);
        if (m > 60) return 0;
        if (m <= 30) return 90 + (30 - m);
        return 70 + (60 - m) * 0.5;
      },
    },
    {
      id: 'sin_pistas',
      label: 'Sin Pistas',
      emoji: '🧠',
      weight: 900,
      score: (s) => (s.hintsUsed === 0 ? 95 : 0),
    },
    {
      id: 'tortuga_ganadora',
      label: 'La Tortuga Ganadora',
      emoji: '🐢',
      weight: 740,
      score: (s) => {
        const m = minutes(s.totalTime);
        if (m < 60) return 0;
        return 60 + (m - 60) * 0.3;
      },
    },
    {
      id: 'eureka',
      label: 'Eureka',
      emoji: '💡',
      weight: 730,
      score: (s) => {
        const f = minutes(fastestChallengeTime(s));
        if (f === Infinity || f > 3) return 0;
        return 88 + (3 - f) * 4;
      },
    },
    {
      id: 'sobrevivientes_desafio',
      label: 'Sobrevivientes del Desafío',
      emoji: '🏅',
      weight: 650,
      score: (s) => 60 + minutes(s.totalTime) * 0.3,
    },
  ];

  function pickAward(st) {
    const candidates = AWARDS
      .map((cfg) => ({ cfg, value: cfg.score(st) }))
      .filter((c) => c.value > 0);
    if (!candidates.length) {
      const fallback = AWARDS.find((a) => a.id === 'sobrevivientes_desafio');
      return fallback;
    }
    candidates.sort((a, b) =>
      b.value !== a.value ? b.value - a.value : b.cfg.weight - a.cfg.weight
    );
    return candidates[0].cfg;
  }

  function formatDuration(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}m ${String(s).padStart(2, '0')}s`;
  }

  const award = pickAward(stats);
  const emojiEl = document.getElementById('diploma-award-emoji');
  const titleEl = document.getElementById('diploma-award-title');
  const btn = document.getElementById('download-diploma');

  // Populate award info
  if (emojiEl) emojiEl.textContent = award.emoji;
  if (titleEl) titleEl.textContent = award.label;

  // Populate dynamic stats
  const dipTime = document.getElementById('diplo-time');
  const dipDate = document.getElementById('diplo-date');
  const dipPos  = document.getElementById('diplo-pos');
  const dipRetos = document.getElementById('diplo-retos');
  const dipCert  = document.getElementById('diplo-cert');
  if (dipTime)  dipTime.textContent  = formatDuration(stats.totalTime);
  if (dipDate)  dipDate.textContent  = new Date().toLocaleDateString('es-ES');
  if (dipPos)   dipPos.textContent   = '#' + (stats.position || '-');
  if (dipRetos) dipRetos.textContent = stats.totalChallenges + '/9';
  if (dipCert) {
    const certCode = 'CERT-' + String({{ $partida->id ?? 0 }}).padStart(8, '0');
    dipCert.textContent = '[ ' + certCode + ' ]';
  }

  // Populate watermark
  const wm = document.getElementById('diplo-watermark');
  if (wm) { let t = ''; for (let i = 0; i < 300; i++) t += '{ DAWSCAPE } '; wm.textContent = t; }

  // Download diploma as PNG
  console.log('html2canvas disponible:', typeof html2canvas !== 'undefined');

  if (btn) {
    btn.addEventListener('click', function() {
      var diploma = document.querySelector('.diploma-card');
      if (!diploma) {
        console.error('Elemento .diploma-card no encontrado en el DOM');
        btn.textContent = 'ERROR - Elemento no encontrado';
        return;
      }

      var originalText = btn.textContent;
      btn.textContent = 'GENERANDO...';
      btn.disabled = true;

      function triggerDownload(dataUrl) {
        var link = document.createElement('a');
        link.download = 'diploma-dawscape.png';
        link.href = dataUrl;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        btn.textContent = originalText;
        btn.disabled = false;
      }

      // Fallback: dibujar diploma con Canvas nativo
      function nativeCanvasFallback() {
        console.log('Usando fallback con Canvas nativo');
        var rect = diploma.getBoundingClientRect();
        var scale = 2;
        var canvas = document.createElement('canvas');
        canvas.width = rect.width * scale;
        canvas.height = rect.height * scale;
        var ctx = canvas.getContext('2d');

        // Fondo
        ctx.fillStyle = '#050505';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Borde verde
        ctx.strokeStyle = '#00ff88';
        ctx.lineWidth = 6;
        ctx.strokeRect(3, 3, canvas.width - 6, canvas.height - 6);

        // Borde interior
        ctx.strokeStyle = 'rgba(0,255,136,0.25)';
        ctx.lineWidth = 2;
        ctx.strokeRect(28, 28, canvas.width - 56, canvas.height - 56);

        var centerX = canvas.width / 2;
        var yPos = 80 * scale;

        // System tag
        ctx.fillStyle = 'rgba(0,255,136,0.65)';
        ctx.font = (12 * scale) + 'px monospace';
        ctx.textAlign = 'center';
        ctx.fillText('[ DAWSCAPE // SISTEMA DE CERTIFICACIÓN DIGITAL ]', centerX, yPos);
        yPos += 50 * scale;

        // Título
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold ' + (28 * scale) + 'px monospace';
        ctx.fillText('CERTIFICADO DE AVENTURA', centerX, yPos);
        yPos += 30 * scale;

        // Separador
        ctx.fillStyle = 'rgba(0,255,136,0.45)';
        ctx.font = (10 * scale) + 'px monospace';
        ctx.fillText('════════════════════════════════════════', centerX, yPos);
        yPos += 30 * scale;

        // Texto certifica
        ctx.fillStyle = '#888888';
        ctx.font = (14 * scale) + 'px monospace';
        ctx.fillText('Este diploma certifica que el equipo', centerX, yPos);
        yPos += 40 * scale;

        // Nombre equipo
        ctx.fillStyle = '#00ff88';
        ctx.font = 'bold ' + (26 * scale) + 'px monospace';
        ctx.fillText(stats.teamName.toUpperCase(), centerX, yPos);
        yPos += 30 * scale;

        // Texto completado
        ctx.fillStyle = '#888888';
        ctx.font = (14 * scale) + 'px monospace';
        ctx.fillText('ha completado con éxito el desafío DAWSCAPE', centerX, yPos);
        yPos += 30 * scale;

        // Separador
        ctx.fillStyle = 'rgba(0,255,136,0.45)';
        ctx.font = (10 * scale) + 'px monospace';
        ctx.fillText('════════════════════════════════════════', centerX, yPos);
        yPos += 28 * scale;

        // Premio label
        ctx.fillStyle = 'rgba(0,255,136,0.55)';
        ctx.font = (11 * scale) + 'px monospace';
        ctx.fillText('— ◆ PREMIO OBTENIDO ◆ —', centerX, yPos);
        yPos += 35 * scale;

        // Premio título
        ctx.fillStyle = '#ffd700';
        ctx.font = 'bold ' + (18 * scale) + 'px monospace';
        ctx.fillText(award.label.toUpperCase(), centerX, yPos);
        yPos += 30 * scale;

        // Separador
        ctx.fillStyle = 'rgba(0,255,136,0.45)';
        ctx.font = (10 * scale) + 'px monospace';
        ctx.fillText('════════════════════════════════════════', centerX, yPos);
        yPos += 28 * scale;

        // Stats
        ctx.font = (13 * scale) + 'px monospace';
        ctx.textAlign = 'left';
        var statsX = canvas.width * 0.2;
        ctx.fillStyle = '#777';
        ctx.fillText('Tiempo total: ', statsX, yPos);
        ctx.fillStyle = '#00ff88';
        ctx.fillText(formatDuration(stats.totalTime), statsX + 200 * scale, yPos);

        ctx.fillStyle = '#777';
        ctx.fillText('Fecha del reto: ', statsX + canvas.width * 0.35, yPos);
        ctx.fillStyle = '#00ff88';
        ctx.fillText(new Date().toLocaleDateString('es-ES'), statsX + canvas.width * 0.35 + 220 * scale, yPos);
        yPos += 25 * scale;

        ctx.fillStyle = '#777';
        ctx.fillText('Posición ranking: ', statsX, yPos);
        ctx.fillStyle = '#00ff88';
        ctx.fillText('#' + (stats.position || '-'), statsX + 200 * scale, yPos);

        ctx.fillStyle = '#777';
        ctx.fillText('Retos resueltos: ', statsX + canvas.width * 0.35, yPos);
        ctx.fillStyle = '#00ff88';
        ctx.fillText(stats.totalChallenges + '/9', statsX + canvas.width * 0.35 + 220 * scale, yPos);
        yPos += 30 * scale;

        // Separador final
        ctx.textAlign = 'center';
        ctx.fillStyle = 'rgba(0,255,136,0.45)';
        ctx.font = (10 * scale) + 'px monospace';
        ctx.fillText('════════════════════════════════════════', centerX, yPos);
        yPos += 30 * scale;

        // Footer
        ctx.fillStyle = 'rgba(0,255,136,0.45)';
        ctx.font = (10 * scale) + 'px monospace';
        ctx.textAlign = 'left';
        var certCode = 'CERT-' + String({{ $partida->id ?? 0 }}).padStart(8, '0');
        ctx.fillText('[ ' + certCode + ' ]', 50 * scale, yPos);
        yPos += 18 * scale;
        ctx.fillStyle = '#555';
        ctx.fillText('DAWSCAPE — Escape Room Digital · XTART · 2026', 50 * scale, yPos);

        triggerDownload(canvas.toDataURL('image/png'));
      }

      // Intentar html2canvas primero, fallback a Canvas nativo
      if (typeof html2canvas !== 'undefined') {
        html2canvas(diploma, {
          scale: 2,
          useCORS: true,
          allowTaint: true,
          backgroundColor: '#050505',
          logging: true
        }).then(function(canvas) {
          console.log('Canvas generado correctamente', canvas.width, canvas.height);
          triggerDownload(canvas.toDataURL('image/png'));
        }).catch(function(err) {
          console.error('html2canvas falló:', err, '— usando fallback nativo');
          try { nativeCanvasFallback(); } catch(e) {
            console.error('Fallback nativo falló:', e);
            btn.textContent = 'ERROR - Reintentar';
            btn.disabled = false;
          }
        });
      } else {
        console.warn('html2canvas no disponible, usando Canvas nativo');
        try { nativeCanvasFallback(); } catch(e) {
          console.error('Fallback nativo falló:', e);
          btn.textContent = 'ERROR - Reintentar';
          btn.disabled = false;
        }
      }
    });
  }
})();
</script>

<!-- Firma discreta -->
<div style="position: fixed; bottom: 5px; right: 10px; font-family: monospace; font-size: 0.65rem; color: rgba(255, 255, 255, 0.3); z-index: 9999; text-align: right;">
    DAWSCAPE SYSTEM // DAW <br>
    <a href="{{ route('legal') }}" style="color: rgba(255, 255, 255, 0.3); text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#00ff88'" onmouseout="this.style.color='rgba(255, 255, 255, 0.3)'">AVISO LEGAL Y RGPD</a>
</div>

<!-- FERNANDO — SISTEMA DE VALORACIÓN CON DIÁLOGO -->
<style>
/* ═══ FERNANDO — OVERLAY DIÁLOGO (reutiliza estilos del sistema de pistas) ═══ */
.fernando-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.7);
    z-index: 10000;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    backdrop-filter: blur(4px);
    overflow-y: auto;
}
.fernando-overlay.visible {
    opacity: 1;
    pointer-events: auto;
}

/* --- Personaje arriba --- */
.fernando-character-area {
    margin-top: 3rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    transform: translateY(-30px);
    opacity: 0;
    transition: all 0.4s ease 0.1s;
}
.fernando-overlay.visible .fernando-character-area {
    transform: translateY(0);
    opacity: 1;
}
.fernando-character-frame {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid var(--green, #00ff88);
    overflow: hidden;
    background: linear-gradient(135deg, #041a0e, #0a2f18);
    box-shadow: 0 0 25px rgba(0,255,136,0.4), 0 8px 25px rgba(0,0,0,0.6);
    position: relative;
}
.fernando-character-frame img {
    width: 100%; height: 100%;
    object-fit: cover;
    image-rendering: pixelated;
    display: block;
}
.fernando-character-frame.talking {
    border-color: #66ffaa;
    box-shadow: 0 0 30px rgba(0,255,136,0.6), 0 8px 25px rgba(0,0,0,0.6);
}
.fernando-character-name {
    margin-top: 0.5rem;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.85rem;
    color: var(--green, #00ff88);
    text-shadow: 0 0 10px rgba(0,255,136,0.5);
    letter-spacing: 0.1em;
}

/* --- Bocadillo de diálogo --- */
.fernando-speech-container {
    margin-top: 1.2rem;
    width: 90%;
    max-width: 520px;
    transform: scale(0.85) translateY(15px);
    opacity: 0;
    transition: all 0.4s ease 0.25s;
}
.fernando-overlay.visible .fernando-speech-container {
    transform: scale(1) translateY(0);
    opacity: 1;
}
.fernando-speech-box {
    position: relative;
    background: linear-gradient(180deg, #f8f5e8 0%, #e8e4d4 100%);
    border: 4px solid #2a2a3e;
    border-radius: 14px;
    padding: 1.5rem 1.3rem 1.3rem;
    box-shadow: 0 4px 30px rgba(0,0,0,0.5), inset 0 2px 0 rgba(255,255,255,0.3);
    min-height: 120px;
}
.fernando-speech-box::before {
    content: '';
    position: absolute;
    top: -14px; left: 50%;
    transform: translateX(-50%);
    width: 0; height: 0;
    border-left: 14px solid transparent;
    border-right: 14px solid transparent;
    border-bottom: 14px solid #2a2a3e;
}
.fernando-speech-box::after {
    content: '';
    position: absolute;
    top: -9px; left: 50%;
    transform: translateX(-50%);
    width: 0; height: 0;
    border-left: 11px solid transparent;
    border-right: 11px solid transparent;
    border-bottom: 11px solid #f8f5e8;
}

.fernando-speech-text {
    font-family: 'Share Tech Mono', monospace;
    font-size: clamp(0.85rem, 2.8vw, 1rem);
    color: #1a1a2e;
    line-height: 1.8;
    min-height: 60px;
    word-wrap: break-word;
}
.fernando-speech-text .fernando-cursor {
    display: inline-block;
    width: 8px;
    height: 1.1em;
    background: #1a1a2e;
    margin-left: 2px;
    vertical-align: text-bottom;
    animation: fernando-blink 0.6s steps(2) infinite;
}
@keyframes fernando-blink {
    0%   { opacity: 1; }
    50%  { opacity: 0; }
    100% { opacity: 1; }
}

/* --- Formulario de valoración dentro del bocadillo --- */
.fernando-val-form {
    margin-top: 1.2rem;
    opacity: 0;
    transform: translateY(8px);
    transition: all 0.4s ease;
    pointer-events: none;
}
.fernando-val-form.visible {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}
.fernando-val-stars {
    display: flex; justify-content: center; gap: 10px;
    margin-bottom: 1rem; flex-direction: row-reverse;
}
.fernando-val-stars input { display: none; }
.fernando-val-stars label {
    font-size: 2.2rem; color: #c4c0b0; cursor: pointer;
    transition: color 0.2s, text-shadow 0.2s, transform 0.2s;
}
.fernando-val-stars label:hover,
.fernando-val-stars label:hover ~ label,
.fernando-val-stars input:checked ~ label {
    color: #00cc66; text-shadow: 0 0 12px rgba(0, 204, 102, 0.8);
    transform: scale(1.15);
}
.fernando-val-textarea {
    width: 100%; background: rgba(26,26,46,0.08);
    border: 2px solid #2a2a3e40; border-radius: 8px;
    color: #1a1a2e; padding: 0.65rem; font-family: 'Share Tech Mono', monospace;
    font-size: 0.85rem; resize: none; margin-bottom: 1rem;
}
.fernando-val-textarea:focus {
    outline: none; border-color: #00cc66;
    box-shadow: 0 0 6px rgba(0, 204, 102, 0.3);
}
.fernando-val-textarea::placeholder { color: #999; }
.fernando-val-buttons { display: flex; gap: 0.8rem; justify-content: center; }
.fernando-btn-submit, .fernando-btn-skip {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.8rem; font-weight: 700;
    padding: 0.6rem 1.8rem; border-radius: 8px;
    border: 3px solid; cursor: pointer;
    transition: all 0.2s ease; letter-spacing: 0.08em;
    text-transform: uppercase;
}
.fernando-btn-submit {
    background: linear-gradient(135deg, #00cc66, #009944);
    border-color: #00ff88; color: #fff;
    box-shadow: 0 0 15px rgba(0,255,136,0.3);
}
.fernando-btn-submit:not(.disabled):hover {
    background: linear-gradient(135deg, #00ee77, #00bb55);
    box-shadow: 0 0 25px rgba(0,255,136,0.5);
    transform: scale(1.05);
}
.fernando-btn-submit.disabled {
    opacity: 0.4; cursor: not-allowed;
    border-color: #00ff8860; color: #fff9;
}
.fernando-btn-skip {
    background: linear-gradient(135deg, #666, #444);
    border-color: #888; color: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
.fernando-btn-skip:hover {
    background: linear-gradient(135deg, #777, #555);
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
    transform: scale(1.05);
}
.fernando-val-msg {
    display: none; margin-top: 0.8rem; text-align: center;
    color: #00aa55; font-weight: bold; font-family: 'Orbitron', sans-serif;
    font-size: 0.8rem; letter-spacing: 0.05em;
}

/* --- Botón cerrar --- */
.fernando-close {
    position: absolute; top: -12px; right: -12px;
    width: 32px; height: 32px; border-radius: 50%;
    border: 2px solid #2a2a3e; background: #ff4466; color: #fff;
    font-size: 1rem; font-weight: bold; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s; z-index: 10;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    opacity: 0; pointer-events: none;
}
.fernando-close.visible { opacity: 1; pointer-events: auto; }
.fernando-close:hover { background: #ff2244; transform: scale(1.1); }

@media (max-width: 600px) {
    .fernando-character-frame { width: 90px; height: 90px; }
    .fernando-character-area { margin-top: 1.5rem; }
    .fernando-speech-container { width: 95%; }
    .fernando-speech-box { padding: 1.2rem 1rem 1rem; min-height: 100px; }
    .fernando-speech-text { font-size: 0.82rem; }
    .fernando-val-stars label { font-size: 1.8rem; }
    .fernando-btn-submit, .fernando-btn-skip { padding: 0.5rem 1.2rem; font-size: 0.75rem; }
}
</style>

<!-- Fernando Overlay -->
<div class="fernando-overlay" id="fernandoOverlay">
    <div class="fernando-character-area">
        <div class="fernando-character-frame" id="fernandoCharFrame">
            <img src="/images/pistas/fernando.png" alt="Fernando" id="fernandoCharImg"
                 data-img-closed="/images/pistas/fernando.png"
                 data-img-open="/images/pistas/fernando1.png">
        </div>
        <div class="fernando-character-name">FERNANDO</div>
    </div>

    <div class="fernando-speech-container">
        <div class="fernando-speech-box" id="fernandoSpeechBox">
            <div class="fernando-speech-text" id="fernandoSpeechText"></div>

            <!-- Formulario de valoración (aparece tras el diálogo) -->
            <div class="fernando-val-form" id="fernandoValForm">
                <div class="fernando-val-stars">
                    <input type="radio" name="fEstrellas" id="fStar5" value="5"><label for="fStar5">★</label>
                    <input type="radio" name="fEstrellas" id="fStar4" value="4"><label for="fStar4">★</label>
                    <input type="radio" name="fEstrellas" id="fStar3" value="3"><label for="fStar3">★</label>
                    <input type="radio" name="fEstrellas" id="fStar2" value="2"><label for="fStar2">★</label>
                    <input type="radio" name="fEstrellas" id="fStar1" value="1"><label for="fStar1">★</label>
                </div>
                <textarea id="fernandoComentario" class="fernando-val-textarea" rows="2" placeholder="Déjanos un comentario (opcional)..."></textarea>
                <div class="fernando-val-buttons">
                    <button class="fernando-btn-skip" id="fernandoBtnSkip">Omitir</button>
                    <button class="fernando-btn-submit disabled" id="fernandoBtnSubmit" disabled>Enviar</button>
                </div>
                <div class="fernando-val-msg" id="fernandoValMsg"></div>
            </div>

            <button class="fernando-close" id="fernandoClose" title="Cerrar">✕</button>
        </div>
    </div>
</div>

<script>
(function() {
    // ═══ Verificar si ya se valoró ═══
    var partidaId = '{{ $partida->id ?? '' }}';
    if (localStorage.getItem('dawscape_valorado_' + partidaId)) return;

    // ═══ CONFIG ═══
    var VELOCIDAD_TEXTO = 35;
    var MOUTH_INTERVAL_MS = 150;
    var DIALOGO_LINEAS = [
        '¡Enhorabuena por completar DAWSCAPE!',
        '¿Qué te ha parecido el escape room?',
        'Nos ayudaría mucho si dejas una valoración 😊'
    ];

    // ═══ ESTADO ═══
    var typewriterInterval = null;
    var speakingAudio = null;
    var mouthAnimInterval = null;
    var mouthOpen = false;

    // ═══ ELEMENTOS DOM ═══
    var overlay = document.getElementById('fernandoOverlay');
    var charFrame = document.getElementById('fernandoCharFrame');
    var charImg = document.getElementById('fernandoCharImg');
    var speechText = document.getElementById('fernandoSpeechText');
    var valForm = document.getElementById('fernandoValForm');
    var btnSubmit = document.getElementById('fernandoBtnSubmit');
    var btnSkip = document.getElementById('fernandoBtnSkip');
    var closeBtn = document.getElementById('fernandoClose');
    var valMsg = document.getElementById('fernandoValMsg');
    var stars = document.querySelectorAll('input[name="fEstrellas"]');

    if (!overlay || !charImg) return;

    // ═══ IMÁGENES DE BOCA ═══
    var imgClosed = charImg.getAttribute('data-img-closed');
    var imgOpen = charImg.getAttribute('data-img-open');
    var hasSpeakingFrames = imgClosed !== imgOpen;

    // Precargar imagen boca abierta
    if (hasSpeakingFrames) {
        var preload = new Image();
        preload.src = imgOpen;
    }

    // ═══ ANIMACIÓN DE BOCA ═══
    function iniciarAnimBoca() {
        charFrame.classList.add('talking');
        if (!hasSpeakingFrames) return;
        mouthOpen = false;
        mouthAnimInterval = setInterval(function() {
            mouthOpen = !mouthOpen;
            charImg.src = mouthOpen ? imgOpen : imgClosed;
        }, MOUTH_INTERVAL_MS);
    }

    function detenerAnimBoca() {
        charFrame.classList.remove('talking');
        if (mouthAnimInterval) { clearInterval(mouthAnimInterval); mouthAnimInterval = null; }
        mouthOpen = false;
        if (hasSpeakingFrames) charImg.src = imgClosed;
    }

    // ═══ SONIDO DE HABLA ═══
    var _speakingAudioUrl = ((window.AUDIO_BASE_URL || '/sonido').replace(/\/$/, '')) + '/hablando-pista.mp3';

    function iniciarSonidoHabla() {
        try {
            if (speakingAudio) speakingAudio.pause();
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

    // ═══ TYPEWRITER ═══
    function typewriter(texto, callback) {
        if (typewriterInterval) clearInterval(typewriterInterval);
        speechText.innerHTML = '';
        var i = 0;

        iniciarSonidoHabla();
        iniciarAnimBoca();

        var cursorSpan = document.createElement('span');
        cursorSpan.className = 'fernando-cursor';

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

    // ═══ DIÁLOGO SECUENCIAL ═══
    function iniciarDialogo() {
        overlay.classList.add('visible');

        // Pausar música de fondo si existe
        if (window.audioManager) {
            try { window.audioManager.pauseMusic(); } catch(e) {}
        }

        var lineaActual = 0;

        function siguienteLinea() {
            if (lineaActual < DIALOGO_LINEAS.length) {
                typewriter(DIALOGO_LINEAS[lineaActual], function() {
                    lineaActual++;
                    if (lineaActual < DIALOGO_LINEAS.length) {
                        setTimeout(siguienteLinea, 1200);
                    } else {
                        // Diálogo terminado → mostrar formulario
                        setTimeout(function() {
                            valForm.classList.add('visible');
                            closeBtn.classList.add('visible');
                        }, 800);
                    }
                });
            }
        }

        // Pequeña pausa antes de empezar a hablar
        setTimeout(siguienteLinea, 600);
    }

    // ═══ CERRAR ═══
    function cerrarFernando() {
        overlay.classList.remove('visible');
        valForm.classList.remove('visible');
        closeBtn.classList.remove('visible');
        if (typewriterInterval) { clearInterval(typewriterInterval); typewriterInterval = null; }
        detenerSonidoHabla();
        detenerAnimBoca();
        if (window.audioManager) {
            try { window.audioManager.resumeMusic(); } catch(e) {}
        }
    }

    // ═══ EVENTOS ═══

    // Habilitar botón enviar al seleccionar estrella
    stars.forEach(function(star) {
        star.addEventListener('change', function() {
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('disabled');
        });
    });

    // Cerrar
    closeBtn.addEventListener('click', function() {
        localStorage.setItem('dawscape_valorado_' + partidaId, 'true');
        cerrarFernando();
    });

    // Omitir
    btnSkip.addEventListener('click', function() {
        localStorage.setItem('dawscape_valorado_' + partidaId, 'true');
        // Fernando se despide
        valForm.classList.remove('visible');
        typewriter('¡No pasa nada! Gracias por jugar. ¡Hasta la próxima!', function() {
            setTimeout(cerrarFernando, 1500);
        });
    });

    // Enviar valoración
    btnSubmit.addEventListener('click', function() {
        if (btnSubmit.disabled) return;

        var checkedStar = document.querySelector('input[name="fEstrellas"]:checked');
        if (!checkedStar) return;

        var starsVal = checkedStar.value;
        var comment = document.getElementById('fernandoComentario').value;

        var csrfToken = '';
        var metaCsrf = document.querySelector('meta[name="csrf-token"]');
        if (metaCsrf) {
            csrfToken = metaCsrf.getAttribute('content');
        } else {
            csrfToken = '{{ csrf_token() }}';
        }

        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Enviando...';

        fetch('/valoraciones', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                partida_id: partidaId,
                estrellas: starsVal,
                comentario: comment
            })
        }).then(function(res) { return res.json(); })
          .then(function(data) {
              localStorage.setItem('dawscape_valorado_' + partidaId, 'true');
              valForm.classList.remove('visible');

              // Fernando agradece con diálogo
              var msgGracias = '¡Muchas gracias por tu valoración! Tu feedback nos ayuda a mejorar.';
              if (data.message === 'Ya valorado') {
                  msgGracias = '¡Ya habías valorado antes! Gracias igualmente.';
              }
              typewriter(msgGracias, function() {
                  setTimeout(cerrarFernando, 2000);
              });
          }).catch(function(err) {
              console.error('Error enviando valoración:', err);
              localStorage.setItem('dawscape_valorado_' + partidaId, 'true');
              valForm.classList.remove('visible');
              typewriter('¡Gracias por participar en DAWSCAPE!', function() {
                  setTimeout(cerrarFernando, 2000);
              });
          });
    });

    // ═══ INICIAR AUTOMÁTICAMENTE tras 1,5 segundos ═══
    setTimeout(iniciarDialogo, 1500);
})();
</script>
</body>
</html>
