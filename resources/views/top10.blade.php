<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1920">
    <title>DAWSCAPE LIVE RANKING</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @vite(['resources/js/app.js'])
    <style>
        :root {
            --green: #00ff41;
            --green-dim: #00cc33;
            --green-dark: #003311;
            --green-mid: #005522;
            --bg: #020b04;
            --bg2: #040f06;
            --gold: #ffd700;
            --silver: #b8c8d0;
            --bronze: #cd8a3a;
            --red: #ff3366;
            --cyan: #00e5ff;
            --mono: 'Share Tech Mono', 'Courier New', monospace;
            --orb: 'Orbitron', monospace;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #000;
            font-family: var(--mono);
            color: var(--green);
        }

        .stage {
            position: fixed;
            top: 50%; left: 50%;
            width: 1920px;
            height: 1080px;
            transform-origin: center center;
            background: var(--bg);
            overflow: hidden;
        }

        #matrix-bg {
            position: absolute;
            top: 0; left: 0;
            width: 1920px; height: 1080px;
            opacity: 0.04;
            pointer-events: none;
            z-index: 0;
        }

        .layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1px 1fr;
            grid-template-rows: 80px 1fr 48px;
            width: 1920px;
            height: 1080px;
            gap: 0;
        }

        .header {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--green-mid);
            background: linear-gradient(180deg, #0a1a0d 0%, var(--bg) 100%);
            position: relative;
            gap: 24px;
        }

        .header-title {
            font-family: var(--orb);
            font-size: 32px;
            font-weight: 900;
            color: var(--green);
            letter-spacing: 0.25em;
            text-transform: uppercase;
            text-shadow: 0 0 20px var(--green), 0 0 40px rgba(0,255,65,0.4);
        }

        .header-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            letter-spacing: 0.15em;
            color: var(--green-dim);
            background: var(--green-dark);
            border: 1px solid var(--green-mid);
            padding: 5px 14px;
            text-transform: uppercase;
        }

        .header-clock {
            position: absolute;
            right: 40px;
            font-family: var(--orb);
            font-size: 22px;
            color: var(--green-dim);
            letter-spacing: 0.1em;
        }

        .divider {
            grid-row: 2;
            width: 1px;
            background: linear-gradient(180deg, transparent, var(--green-mid) 20%, var(--green-mid) 80%, transparent);
        }

        .panel {
            grid-row: 2;
            display: flex;
            flex-direction: column;
            padding: 32px 48px 52px;
            overflow: hidden;
        }

        .panel-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--green-dark);
        }

        .panel-label {
            font-family: var(--orb);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.3em;
            text-transform: uppercase;
        }

        .panel-label.finished { color: var(--gold); }
        .panel-label.live { color: var(--cyan); }

        .panel-count {
            font-size: 12px;
            color: var(--green-dim);
            letter-spacing: 0.1em;
            background: var(--green-dark);
            padding: 3px 10px;
            border: 1px solid var(--green-mid);
        }

        .live-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: var(--red);
            box-shadow: 0 0 8px var(--red);
            animation: livePulse 1.4s ease-in-out infinite;
        }
        @keyframes livePulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .podium-container {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
            height: 340px;
            flex-shrink: 0;
        }

        .podium-slot {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .podium-avatar {
            width: 80px; height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--orb);
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 10px;
            position: relative;
        }

        .podium-avatar.rank-1 {
            width: 96px; height: 96px;
            font-size: 34px;
            border: 3px solid var(--gold);
            background: radial-gradient(circle at 35% 35%, #3d2f00, #1a1200);
            color: var(--gold);
            box-shadow: 0 0 24px rgba(255,215,0,0.5), 0 0 48px rgba(255,215,0,0.2);
        }
        .podium-avatar.rank-2 {
            border: 2px solid var(--silver);
            background: radial-gradient(circle at 35% 35%, #1d2830, #0d1318);
            color: var(--silver);
            box-shadow: 0 0 14px rgba(184,200,208,0.3);
        }
        .podium-avatar.rank-3 {
            border: 2px solid var(--bronze);
            background: radial-gradient(circle at 35% 35%, #2a1a08, #100a03);
            color: var(--bronze);
            box-shadow: 0 0 14px rgba(205,138,58,0.3);
        }

        .podium-crown {
            position: absolute;
            top: -22px;
            font-size: 20px;
        }

        .podium-name {
            font-family: var(--orb);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 5px;
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .podium-name.rank-1 { color: var(--gold); font-size: 18px; }
        .podium-name.rank-2 { color: var(--silver); }
        .podium-name.rank-3 { color: var(--bronze); }

        .podium-medals {
            display: inline-flex;
            gap: 6px;
            justify-content: center;
            margin-bottom: 6px;
            min-height: 18px;
        }

        .podium-time {
            font-size: 20px;
            letter-spacing: 0.08em;
            text-align: center;
            margin-bottom: 12px;
        }
        .podium-time.rank-1 { color: var(--gold); font-size: 24px; text-shadow: 0 0 12px rgba(255,215,0,0.6); }
        .podium-time.rank-2 { color: var(--silver); }
        .podium-time.rank-3 { color: var(--bronze); }

        .podium-status {
            font-size: 11px;
            color: var(--green-dim);
            letter-spacing: 0.2em;
            text-align: center;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .podium-status.timeout { color: var(--red); }

        .podium-base {
            width: 170px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 8px;
            border: 1px solid;
            position: relative;
        }

        .podium-base.rank-1 {
            height: 110px;
            background: linear-gradient(180deg, rgba(255,215,0,0.12) 0%, rgba(255,215,0,0.04) 100%);
            border-color: var(--gold);
            box-shadow: 0 0 20px rgba(255,215,0,0.15), inset 0 1px 0 rgba(255,215,0,0.3);
            width: 190px;
        }
        .podium-base.rank-2 {
            height: 78px;
            background: linear-gradient(180deg, rgba(184,200,208,0.08) 0%, transparent 100%);
            border-color: var(--silver);
        }
        .podium-base.rank-3 {
            height: 56px;
            background: linear-gradient(180deg, rgba(205,138,58,0.08) 0%, transparent 100%);
            border-color: var(--bronze);
        }

        .podium-rank-number {
            font-family: var(--orb);
            font-size: 32px;
            font-weight: 900;
            opacity: 0.6;
        }
        .podium-rank-number.rank-1 { color: var(--gold); font-size: 40px; }
        .podium-rank-number.rank-2 { color: var(--silver); }
        .podium-rank-number.rank-3 { color: var(--bronze); }

        .finished-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
            overflow: hidden;
        }

        .finished-row {
            display: grid;
            grid-template-columns: 36px 42px 1fr auto;
            align-items: center;
            gap: 16px;
            padding: 10px 16px;
            border: 1px solid var(--green-dark);
            background: rgba(0,255,65,0.02);
            transition: background 0.2s;
        }
        .finished-row.timeout { opacity: 0.5; }
        .finished-row:hover { background: rgba(0,255,65,0.05); }

        .row-rank {
            font-family: var(--orb);
            font-size: 15px;
            color: var(--green-dim);
            text-align: center;
        }

        .row-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 1px solid var(--green-mid);
            background: var(--green-dark);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            font-family: var(--orb);
            font-weight: 700;
            color: var(--green);
        }

        .row-name {
            font-size: 16px;
            color: var(--green);
            letter-spacing: 0.05em;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .row-time {
            font-family: var(--orb);
            font-size: 17px;
            color: var(--green-dim);
            letter-spacing: 0.06em;
            text-align: right;
        }

        .live-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            flex: 1;
            overflow: hidden;
        }

        .live-card {
            display: grid;
            grid-template-columns: 48px 44px 1fr auto;
            align-items: center;
            gap: 18px;
            padding: 16px 20px;
            border: 1px solid var(--green-mid);
            background: rgba(0,255,65,0.03);
            position: relative;
            overflow: hidden;
        }

        .live-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 3px; height: 100%;
            background: var(--cyan);
            opacity: 0.6;
        }

        .live-card.leader::before {
            background: var(--green);
            box-shadow: 0 0 8px var(--green);
            opacity: 1;
        }

        .live-pos {
            font-family: var(--orb);
            font-size: 22px;
            font-weight: 900;
            color: var(--cyan);
            text-align: center;
        }

        .live-card.leader .live-pos { color: var(--green); text-shadow: 0 0 10px var(--green); }

        .live-avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            border: 1px solid var(--cyan);
            background: rgba(0,229,255,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-family: var(--orb);
            font-weight: 700;
            color: var(--cyan);
        }

        .live-card.leader .live-avatar {
            border-color: var(--green);
            background: rgba(0,255,65,0.1);
            color: var(--green);
            box-shadow: 0 0 12px rgba(0,255,65,0.3);
        }

        .live-info { display: flex; flex-direction: column; gap: 8px; min-width: 0; }

        .live-name {
            font-family: var(--orb);
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--green);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .live-progress-wrap { display: flex; align-items: center; gap: 10px; }

        .live-progress-bar {
            flex: 1;
            height: 5px;
            background: var(--green-dark);
            border: 1px solid var(--green-mid);
            position: relative;
            overflow: hidden;
        }

        .live-progress-fill {
            height: 100%;
            background: var(--cyan);
            transition: width 0.5s ease;
        }

        .live-card.leader .live-progress-fill {
            background: var(--green);
            box-shadow: 0 0 6px var(--green);
        }

        .live-hints {
            font-size: 12px;
            color: var(--green-dim);
            letter-spacing: 0.08em;
            white-space: nowrap;
        }

        .live-timer-col { display: flex; flex-direction: column; align-items: flex-end; gap: 5px; }

        .live-timer {
            font-family: var(--orb);
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 0.06em;
            color: var(--cyan);
        }

        .live-card.leader .live-timer {
            color: var(--green);
            text-shadow: 0 0 12px rgba(0,255,65,0.5);
        }

        .live-diff {
            font-size: 12px;
            color: var(--red);
            letter-spacing: 0.06em;
            text-align: right;
        }
        .live-diff.ahead { color: var(--green-dim); }

        .empty-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            color: var(--green-dim);
            opacity: 0.6;
            gap: 14px;
            text-align: center;
        }
        .empty-panel i { font-size: 52px; opacity: 0.4; }
        .empty-panel .empty-text {
            font-family: var(--orb);
            font-size: 13px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }

        .scanlines {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 100;
            background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.04) 2px, rgba(0,0,0,0.04) 4px);
        }

        .corner {
            position: absolute;
            width: 40px; height: 40px;
            z-index: 10;
            opacity: 0.4;
        }
        .corner-tl { top: 8px; left: 8px; border-top: 2px solid var(--green); border-left: 2px solid var(--green); }
        .corner-tr { top: 8px; right: 8px; border-top: 2px solid var(--green); border-right: 2px solid var(--green); }
        .corner-bl { bottom: 8px; left: 8px; border-bottom: 2px solid var(--green); border-left: 2px solid var(--green); }
        .corner-br { bottom: 8px; right: 8px; border-bottom: 2px solid var(--green); border-right: 2px solid var(--green); }

        .section-tag {
            font-size: 11px;
            letter-spacing: 0.25em;
            color: var(--green-dim);
            opacity: 0.6;
            text-transform: uppercase;
        }

        .separator-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, var(--green-dark), transparent);
        }

        .ig-ticker {
            grid-column: 1 / -1;
            grid-row: 3;
            display: flex;
            align-items: center;
            overflow: hidden;
            border-top: 1px solid var(--green-mid);
            background: linear-gradient(90deg, #040f06, #020b04 50%, #040f06);
            position: relative;
            z-index: 10;
        }

        .ig-ticker-track {
            display: flex;
            align-items: center;
            animation: igScroll 40s linear infinite;
            white-space: nowrap;
            gap: 0;
        }

        .ig-ticker:hover .ig-ticker-track { animation-play-state: paused; }

        @keyframes igScroll {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .ig-brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0 28px;
            font-family: var(--orb);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.25em;
            color: var(--green);
            text-transform: uppercase;
            white-space: nowrap;
            text-shadow: 0 0 10px rgba(0,255,65,0.4);
        }

        .ig-brand svg { flex-shrink: 0; }

        .ig-handle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0 28px;
            font-size: 14px;
            letter-spacing: 0.08em;
            color: var(--cyan);
            opacity: 0.85;
            white-space: nowrap;
        }

        .ig-handle::before {
            content: '';
            display: inline-block;
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--green-mid);
            flex-shrink: 0;
        }

        .ig-at {
            color: var(--green-dim);
            font-size: 12px;
            margin-right: 2px;
        }

        .ig-ticker::before, .ig-ticker::after {
            content: '';
            position: absolute;
            top: 0;
            width: 40px; height: 100%;
            pointer-events: none;
            z-index: 2;
        }
        .ig-ticker::before { left: 0; background: linear-gradient(90deg, var(--bg), transparent); }
        .ig-ticker::after  { right: 0; background: linear-gradient(90deg, transparent, var(--bg)); }

        .medal-icon {
            font-size: 16px;
            filter: drop-shadow(0 0 4px currentColor);
            vertical-align: middle;
        }

        .move-up-flash { animation: flashGreen 2s; }
        .move-down-flash { animation: flashRed 2s; }
        @keyframes flashGreen { 0% { background-color: rgba(0,255,65,0.25); } 100% { background-color: transparent; } }
        @keyframes flashRed   { 0% { background-color: rgba(255,34,68,0.25); } 100% { background-color: transparent; } }

        #celebration-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.9);
            backdrop-filter: blur(5px);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s;
        }
        #celebration-overlay.active { opacity: 1; pointer-events: all; }
        .celeb-title {
            font-size: 3rem;
            color: var(--green);
            font-weight: bold;
            font-family: var(--mono);
            text-transform: uppercase;
            text-shadow: 0 0 8px rgba(0,255,65,0.5);
            margin-bottom: 0;
        }
        .celeb-title::before { content: '>> ACCESS GRANTED // '; }
        .celeb-team {
            font-size: 5rem;
            color: #ffffff;
            font-weight: bold;
            margin: 10px 0;
            animation: popIn 1s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.2s both, textGlow 2s infinite alternate;
            text-shadow: 0 0 10px var(--green);
        }
        .celeb-time {
            font-size: 2.5rem;
            color: var(--gold);
            font-weight: bold;
            text-shadow: 0 0 6px rgba(255,204,0,0.5);
            animation: popIn 1s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.4s both;
        }
        @keyframes textGlow {
            0% { text-shadow: 0 0 10px var(--green), 0 0 20px var(--green); }
            100% { text-shadow: 0 0 20px var(--green), 0 0 40px var(--green), 0 0 60px #ffffff; }
        }
        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        #confetti { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: -1; }

        .legal-footer {
            position: fixed;
            bottom: 52px;
            right: 14px;
            font-family: var(--mono);
            font-size: 10px;
            color: rgba(0, 255, 65, 0.35);
            z-index: 9999;
            text-align: right;
            letter-spacing: 0.08em;
        }
        .legal-footer a {
            color: rgba(0, 255, 65, 0.45);
            text-decoration: none;
            transition: color 0.3s;
        }
        .legal-footer a:hover { color: var(--green); }

        ::-webkit-scrollbar { display: none; }
        * { scrollbar-width: none; -ms-overflow-style: none; }
    </style>
</head>
<body>

<div class="stage" id="stage">
  <canvas id="matrix-bg" width="1920" height="1080"></canvas>
  <div class="scanlines"></div>
  <div class="corner corner-tl"></div>
  <div class="corner corner-tr"></div>
  <div class="corner corner-bl"></div>
  <div class="corner corner-br"></div>

  <div class="layout">

    <header class="header">
      <span class="section-tag">&gt;_</span>
      <h1 class="header-title">DAWSCAPE LIVE RANKING</h1>
      <div class="header-badge">
        <div class="live-dot"></div>
        <span>SESIÓN ACTIVA</span>
      </div>
      <div class="header-clock" id="clock">--:--:--</div>
    </header>

    <div class="panel" id="finished-panel">
      <div class="panel-header">
        <span class="panel-label finished">▸ TERMINADOS</span>
        <span class="panel-count" id="finished-count">0 EQUIPOS</span>
        <div class="separator-line"></div>
        <span class="section-tag">RANKING FINAL</span>
      </div>

      <div class="podium-container" id="podium-container"></div>
      <div class="finished-list" id="finished-list"></div>

      <div class="empty-panel" id="finished-empty" style="display:none;">
        <i class="ph ph-trophy"></i>
        <div class="empty-text">Esperando primeros finalistas...</div>
      </div>
    </div>

    <div class="divider"></div>

    <div class="panel" id="live-panel">
      <div class="panel-header">
        <div class="live-dot"></div>
        <span class="panel-label live">EN VIVO</span>
        <span class="panel-count" id="live-count">0 EQUIPOS</span>
        <div class="separator-line"></div>
        <span class="section-tag">EN PROGRESO</span>
      </div>

      <div class="live-list" id="live-list"></div>

      <div class="empty-panel" id="live-empty" style="display:none;">
        <i class="ph ph-terminal-window"></i>
        <div class="empty-text">Sin partidas en curso</div>
      </div>
    </div>

    <div class="ig-ticker">
      <div class="ig-ticker-track" id="ig-track"></div>
    </div>

  </div>
</div>

<div id="celebration-overlay">
  <canvas id="confetti"></canvas>
  <div class="celeb-title">SISTEMA COMPROMETIDO</div>
  <div class="celeb-team" id="celeb-team-name">NOMBRE_EQUIPO</div>
  <div class="celeb-time" id="celeb-team-time">00m 00s</div>
</div>

<div class="legal-footer">
  DAWSCAPE_OS v1.0 // EOF<br>
  <a href="{{ route('legal') }}">SYS_POLICY</a>
</div>

<script>
// ── STATE ──
let previousTeamsState = {};
let knownFinishers = new Set();

// ── UTILS ──
function getInitials(name) {
    if (!name) return '??';
    const clean = String(name).trim();
    const parts = clean.split(/\s+/).filter(Boolean);
    if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
    const letters = clean.replace(/[^A-Za-zÁÉÍÓÚÑáéíóúñ0-9]/g, '');
    return (letters.slice(0, 2) || '??').toUpperCase();
}

function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
}

function renderMedals(logros) {
    if (!logros || logros.length === 0) return '';
    let html = '';
    logros.forEach(l => {
        if (l === 'speedrunner_ninja') html += '<i class="ph-fill ph-lightning medal-icon" title="Speedrunner Ninja" style="color:#eab308;"></i>';
        if (l === 'cerebro_intacto')   html += '<i class="ph-fill ph-brain medal-icon" title="Cerebro Intacto" style="color:#ec4899;"></i>';
        if (l === 'hackerman')         html += '<i class="ph-fill ph-terminal-window medal-icon" title="Hackerman" style="color:#10b981;"></i>';
    });
    return html;
}

function formatDiff(secs) {
    const abs = Math.abs(Math.round(secs));
    const h = Math.floor(abs / 3600);
    const m = Math.floor((abs % 3600) / 60);
    const s = abs % 60;
    if (h > 0) return `${h}h ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`;
    return `${m}m ${String(s).padStart(2,'0')}s`;
}

// ── CLOCK ──
function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent =
        String(now.getHours()).padStart(2,'0') + ':' +
        String(now.getMinutes()).padStart(2,'0') + ':' +
        String(now.getSeconds()).padStart(2,'0');
}
updateClock();
setInterval(updateClock, 1000);

// ── MATRIX RAIN BG ──
(function () {
    const canvas = document.getElementById('matrix-bg');
    const ctx = canvas.getContext('2d');
    canvas.width = 1920;
    canvas.height = 1080;
    const fontSize = 18;
    const cols = Math.floor(1920 / fontSize);
    const drops = Array(cols).fill(1);
    const chars = 'アイウエオカキクケコ01アBCDEFGHIJKLMNOPQRSTUVWXYZ0123456789$#@%&';
    function draw() {
        ctx.fillStyle = 'rgba(2,11,4,0.05)';
        ctx.fillRect(0, 0, 1920, 1080);
        ctx.fillStyle = '#00ff41';
        ctx.font = fontSize + "px 'Share Tech Mono', monospace";
        for (let i = 0; i < drops.length; i++) {
            const c = chars[Math.floor(Math.random() * chars.length)];
            ctx.fillText(c, i * fontSize, drops[i] * fontSize);
            if (drops[i] * fontSize > 1080 && Math.random() > 0.975) drops[i] = 0;
            drops[i]++;
        }
    }
    setInterval(draw, 60);
})();

// ── CELEBRATION (matrix rain overlay) ──
const confettiCanvas = document.getElementById('confetti');
const cctx = confettiCanvas.getContext('2d');
let cCols = 0, cDrops = [];
const cFontSize = 20;
let celebActive = false;

function initCelebration() {
    confettiCanvas.width = window.innerWidth;
    confettiCanvas.height = window.innerHeight;
    cCols = Math.floor(confettiCanvas.width / cFontSize);
    cDrops = Array(cCols).fill(0).map(() => Math.random() * -100);
}
function drawCelebration() {
    if (!celebActive) {
        cctx.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);
        return;
    }
    cctx.fillStyle = 'rgba(0,0,0,0.1)';
    cctx.fillRect(0, 0, confettiCanvas.width, confettiCanvas.height);
    cctx.fillStyle = '#00ff41';
    cctx.font = cFontSize + "px 'Share Tech Mono', monospace";
    for (let i = 0; i < cDrops.length; i++) {
        const t = Math.random() > 0.5 ? '1' : '0';
        cctx.fillText(t, i * cFontSize, cDrops[i] * cFontSize);
        if (cDrops[i] * cFontSize > confettiCanvas.height && Math.random() > 0.95) cDrops[i] = 0;
        cDrops[i] += 1.5;
    }
    requestAnimationFrame(drawCelebration);
}
function triggerCelebration(teamName, timeFormatted) {
    const overlay = document.getElementById('celebration-overlay');
    document.getElementById('celeb-team-name').innerText = teamName;
    document.getElementById('celeb-team-time').innerText = 'TIEMPO: ' + timeFormatted;
    overlay.classList.add('active');
    celebActive = true;
    initCelebration();
    drawCelebration();
    setTimeout(() => {
        celebActive = false;
        overlay.classList.remove('active');
    }, 7000);
}
window.addEventListener('resize', () => { if (celebActive) initCelebration(); });

// ── RENDER: PODIUM (2-1-3 order) ──
function renderPodium(top3) {
    const container = document.getElementById('podium-container');
    if (top3.length === 0) {
        container.innerHTML = '';
        return;
    }

    const slots = [];
    if (top3[1]) slots.push({ team: top3[1], rank: 2 });
    else slots.push(null);
    if (top3[0]) slots.push({ team: top3[0], rank: 1 });
    else slots.push(null);
    if (top3[2]) slots.push({ team: top3[2], rank: 3 });
    else slots.push(null);

    container.innerHTML = slots.map(slot => {
        if (!slot) return `<div class="podium-slot"></div>`;
        const { team, rank } = slot;
        const initials = getInitials(team.nombre_equipo);
        const crown = rank === 1 ? `<span class="podium-crown">👑</span>` : '';
        const isTimeout = team.estado === 'timeout';
        const statusTxt = isTimeout ? 'ERR_TIMEOUT' : (team.gap || '');
        const statusCls = isTimeout ? 'timeout' : '';
        return `
          <div class="podium-slot">
            <div class="podium-avatar rank-${rank}">
              ${crown}
              ${escapeHtml(initials)}
            </div>
            <div class="podium-name rank-${rank}">${escapeHtml(team.nombre_equipo)}</div>
            <div class="podium-medals">${renderMedals(team.logros)}</div>
            <div class="podium-time rank-${rank}">${escapeHtml(team.tiempo_formateado)}</div>
            <div class="podium-status ${statusCls}">${escapeHtml(statusTxt)}</div>
            <div class="podium-base rank-${rank}">
              <span class="podium-rank-number rank-${rank}">0${rank}</span>
            </div>
          </div>
        `;
    }).join('');
}

// ── RENDER: FINISHED LIST (4+) ──
function renderFinishedList(rest, totalFinished) {
    const list = document.getElementById('finished-list');
    document.getElementById('finished-count').textContent = `${totalFinished} ${totalFinished === 1 ? 'EQUIPO' : 'EQUIPOS'}`;

    list.innerHTML = rest.map((team, i) => {
        const rank = team.rank ?? (i + 4);
        const initials = getInitials(team.nombre_equipo);
        const isTimeout = team.estado === 'timeout';
        const color = team.color_hex || '#00ff41';
        const medals = renderMedals(team.logros);
        return `
          <div class="finished-row ${isTimeout ? 'timeout' : ''}">
            <span class="row-rank">${String(rank).padStart(2, '0')}</span>
            <div class="row-avatar" style="border-color:${color}55; color:${color};">${escapeHtml(initials)}</div>
            <span class="row-name">${escapeHtml(team.nombre_equipo)} ${medals}</span>
            <span class="row-time">${escapeHtml(team.tiempo_formateado)}${isTimeout ? ' · TIMEOUT' : ''}</span>
          </div>
        `;
    }).join('');
}

// ── RENDER: LIVE CARDS ──
function renderLive(liveTeams, bestFinishedSecs) {
    const list = document.getElementById('live-list');
    const emptyEl = document.getElementById('live-empty');
    document.getElementById('live-count').textContent = `${liveTeams.length} ${liveTeams.length === 1 ? 'EQUIPO' : 'EQUIPOS'}`;

    if (liveTeams.length === 0) {
        list.innerHTML = '';
        list.style.display = 'none';
        emptyEl.style.display = 'flex';
        return;
    }
    list.style.display = 'flex';
    emptyEl.style.display = 'none';

    // liveTeams already arrive sorted by peso desc + tiempo asc from server
    const leaderSecs = (bestFinishedSecs !== null && bestFinishedSecs !== undefined)
        ? bestFinishedSecs
        : (liveTeams[0] ? liveTeams[0].tiempo_segundos : 0);

    list.innerHTML = liveTeams.map((team, i) => {
        const pos = i + 1;
        const isLeader = pos === 1;
        const initials = getInitials(team.nombre_equipo);
        const color = team.color_hex || '#00e5ff';
        const diffSecs = team.tiempo_segundos - leaderSecs;
        let diffHtml;
        if (isLeader) {
            diffHtml = '<div class="live-diff ahead">LÍDER EN VIVO</div>';
        } else if (diffSecs >= 0) {
            diffHtml = `<div class="live-diff">+${formatDiff(diffSecs)} vs mejor</div>`;
        } else {
            diffHtml = `<div class="live-diff ahead">-${formatDiff(diffSecs)} vs mejor</div>`;
        }
        const hints = (team.pistas_usadas ?? 0);
        const progress = Math.max(0, Math.min(100, Math.round(team.progreso_porcentaje ?? 0)));
        const medals = renderMedals(team.logros);
        const oldRank = previousTeamsState[team.id] ? previousTeamsState[team.id].rank : pos;
        let moveCls = '';
        if (pos < oldRank) moveCls = 'move-up-flash';
        else if (pos > oldRank) moveCls = 'move-down-flash';

        const avatarStyle = isLeader ? '' : `border-color:${color}88; color:${color};`;

        return `
          <div class="live-card ${isLeader ? 'leader' : ''} ${moveCls}">
            <div class="live-pos">${String(pos).padStart(2, '0')}</div>
            <div class="live-avatar" style="${avatarStyle}">${escapeHtml(initials)}</div>
            <div class="live-info">
              <div class="live-name">${escapeHtml(team.nombre_equipo)} ${medals}</div>
              <div class="live-progress-wrap">
                <div class="live-progress-bar">
                  <div class="live-progress-fill" style="width:${progress}%"></div>
                </div>
                <span class="live-hints">${progress}% · ${hints} ${hints === 1 ? 'pista' : 'pistas'}</span>
              </div>
            </div>
            <div class="live-timer-col">
              <div class="live-timer">${escapeHtml(team.tiempo_formateado)}</div>
              ${diffHtml}
            </div>
          </div>
        `;
    }).join('');
}

// ── RENDER: dashboard (main entry) ──
function renderDashboard(data) {
    const finished = Array.isArray(data.finalizados)
        ? data.finalizados
        : (Array.isArray(data.top10) ? data.top10 : []);
    const live = Array.isArray(data.en_progreso) ? data.en_progreso : [];

    // Podium (top 3 of finished only)
    const top3 = finished.slice(0, 3);
    const rest = finished.slice(3);
    renderPodium(top3);
    renderFinishedList(rest, finished.length);

    // Empty state for finished panel
    const emptyFin = document.getElementById('finished-empty');
    const podiumContainer = document.getElementById('podium-container');
    const finishedList = document.getElementById('finished-list');
    if (finished.length === 0) {
        podiumContainer.style.display = 'none';
        finishedList.style.display = 'none';
        emptyFin.style.display = 'flex';
    } else {
        podiumContainer.style.display = 'flex';
        finishedList.style.display = 'flex';
        emptyFin.style.display = 'none';
    }

    // Best finished time (excluding timeouts) to serve as live leader reference
    const bestFinishedSecs = finished
        .filter(t => t.estado === 'finalizado')
        .reduce((min, t) => (min === null || t.tiempo_segundos < min) ? t.tiempo_segundos : min, null);

    renderLive(live, bestFinishedSecs);

    // Celebration for newly finished teams
    finished.forEach(t => {
        if (t.estado === 'finalizado' && !knownFinishers.has(t.id)) {
            knownFinishers.add(t.id);
            // Only celebrate if we've already rendered at least once (avoid initial burst)
            if (Object.keys(previousTeamsState).length > 0) {
                triggerCelebration(t.nombre_equipo, t.tiempo_formateado);
            }
        }
    });
    if (Object.keys(previousTeamsState).length === 0) {
        // First render: seed known finishers without celebrating
        finished.forEach(t => knownFinishers.add(t.id));
    }

    // Save rank state for next tick (used to flash move-up/down in live list)
    const newState = {};
    live.forEach((t, i) => { newState[t.id] = { rank: i + 1 }; });
    previousTeamsState = newState;
}

// ── FETCH ──
async function fetchTop10() {
    try {
        const r = await fetch('/top10/datos');
        if (!r.ok) return;
        const data = await r.json();
        renderDashboard(data || { top10: [], finalizados: [], en_progreso: [] });
    } catch (e) {
        console.error('Top10 fetch error', e);
    }
}

fetchTop10();
setInterval(fetchTop10, 5000);

document.addEventListener('DOMContentLoaded', () => {
    if (window.Echo) {
        window.Echo.channel('top10').listen('Top10Updated', () => fetchTop10());
    }
});

// ── IG TICKER ──
(function () {
    const handles = [
        '@miguelmrtt', '@angeelmnzz13', '@elooymcm.9._', '@arturomrtnezz',
        '@nachomc361', '@miguelangelserr_', '@yoseth.777', '@marioveeraa'
    ];
    const brand = `
      <span class="ig-brand">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <rect x="2" y="2" width="20" height="20" rx="5"/>
          <circle cx="12" cy="12" r="4"/>
          <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
        </svg>
        INSTAGRAM · DAW_TEAM
      </span>
    `;
    function buildSet() {
        return brand + handles.map(h =>
            `<span class="ig-handle"><span class="ig-at">@</span>${escapeHtml(h.replace('@', ''))}</span>`
        ).join('');
    }
    const track = document.getElementById('ig-track');
    track.innerHTML = buildSet() + buildSet();
})();

// ── SCALE STAGE TO FIT VIEWPORT ──
function scaleStage() {
    const stage = document.getElementById('stage');
    const sx = window.innerWidth / 1920;
    const sy = window.innerHeight / 1080;
    const s = Math.min(sx, sy);
    stage.style.transform = `translate(-50%, -50%) scale(${s})`;
}
scaleStage();
window.addEventListener('resize', scaleStage);
</script>

</body>
</html>
