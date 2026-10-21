<!-- Botón de música (play/pausa) -->
<button id="musicToggleBtn" class="music-mute-btn" type="button" title="Reproducir / Pausar música">
  <span id="musicToggleIcon">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="6,4 20,12 6,20"/></svg>
  </span>
</button>

<!-- Indicador global de estado de música -->
<div class="now-playing-banner" id="nowPlayingBanner" aria-live="polite">
  <span class="now-playing-label" id="nowPlayingPrefix">♪ Sonando ahora mismo:</span>
  <span class="now-playing-title" id="nowPlayingTitle">—</span>
  <span class="now-playing-label" id="nowPlayingSuffix">♪</span>
</div>

<style>
  :root {
    --music-player-primary: #00ff88;
    --music-player-bg: rgba(2, 12, 6, 0.92);
    --music-player-border: #00ff8840;
    --music-player-text: #c8ffdf;
    --music-player-panel: #041a0e;
  }

  /* Indicador de estado de música (reproduciendo/pausada) — ~0.5s total (salida 0.25s + entrada 0.25s) */
  .now-playing-banner {
    position: fixed;
    inset-inline: 0;
    bottom: 0.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    font-family: 'Share Tech Mono', monospace;
    font-size: 0.75rem;
    color: var(--music-player-primary);
    text-shadow: 0 0 10px rgba(0, 255, 136, 0.7);
    pointer-events: none;
    z-index: 850;
    opacity: 0;
    transform: translateX(-120%);
    transition: opacity 0.25s ease, transform 0.25s ease;
  }

  .now-playing-banner.visible {
    opacity: 0.9;
    transform: translateX(0);
  }

  /* Salida: deslizar hacia la derecha (0.25s) */
  .now-playing-banner.visible.leaving {
    opacity: 0;
    transform: translateX(120%);
  }

  .now-playing-title {
    font-weight: 700;
    max-width: 90vw;
    overflow: visible;
    white-space: normal;
    word-break: keep-all;
    text-align: center;
    animation: nowPlayingPulse 2.2s ease-in-out infinite;
  }

  .now-playing-label {
    opacity: 0.8;
  }

  @keyframes nowPlayingPulse {
    0%, 100% { text-shadow: 0 0 10px rgba(0, 255, 136, 0.6); }
    50% { text-shadow: 0 0 18px rgba(0, 255, 136, 0.95); }
  }

  /* Botón flotante, pequeño y discreto en esquina inferior izquierda */
  .music-mute-btn {
    position: fixed;
    bottom: 3.5rem;
    left: 0.75rem;
    z-index: 900;
    width: 38px;
    height: 38px;
    border-radius: 999px;
    border: 1px solid var(--music-player-border);
    background: rgba(2, 12, 6, 0.9);
    color: var(--music-player-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 255, 136, 0.2);
    backdrop-filter: blur(6px);
    -webkit-tap-highlight-color: transparent;
    transition:
      transform 0.18s ease,
      box-shadow 0.18s ease,
      border-color 0.18s ease,
      background-color 0.18s ease,
      opacity 0.18s ease;
    opacity: 0.9;
  }

  .music-mute-btn:hover {
    transform: translateY(-1px) scale(1.04);
    box-shadow: 0 6px 18px rgba(0, 255, 136, 0.35);
    border-color: var(--music-player-primary);
    opacity: 1;
  }

  .music-mute-btn:active {
    transform: translateY(0) scale(0.96);
    box-shadow: 0 2px 8px rgba(0, 255, 136, 0.2);
  }



  /* Mobile: ligeramente más pegado al borde y sin molestar al contenido */
  @media (max-width: 600px) {
    .now-playing-banner {
      font-size: 0.65rem;
      padding: 0 0.5rem;
    }

    .now-playing-title {
      max-width: 100vw;
    }

    .music-mute-btn {
      bottom: 0.6rem;
      left: 0.6rem;
      width: 34px;
      height: 34px;
      font-size: 1rem;
      box-shadow: 0 3px 10px rgba(0, 255, 136, 0.25);
    }


  }
</style>

<script>
  (function() {
    const toggleBtn = document.getElementById('musicToggleBtn');
    const toggleIcon = document.getElementById('musicToggleIcon');
    const nowPlayingBanner = document.getElementById('nowPlayingBanner');
    const nowPlayingTitle = document.getElementById('nowPlayingTitle');
    const nowPlayingPrefix = document.getElementById('nowPlayingPrefix');
    const nowPlayingSuffix = document.getElementById('nowPlayingSuffix');
    if (!toggleBtn || !toggleIcon || !nowPlayingBanner) return;

    let currentMode = 'hidden'; // 'playing' | 'paused' | 'hidden'
    let pauseHintTimeout = null;

    function updateIcon(isPlaying) {
      toggleIcon.innerHTML = isPlaying
        ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><rect x="5" y="4" width="4" height="16"/><rect x="15" y="4" width="4" height="16"/></svg>'
        : '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="6,4 20,12 6,20"/></svg>';
    }

    function clearPauseTimeout() {
      if (pauseHintTimeout) {
        clearTimeout(pauseHintTimeout);
        pauseHintTimeout = null;
      }
    }

    function setBannerHidden() {
      clearPauseTimeout();
      if (currentMode === 'hidden') return;
      // Salir deslizando a la derecha (mantener .visible para que parta del centro)
      nowPlayingBanner.classList.add('leaving');
      const onOut = () => {
        nowPlayingBanner.classList.remove('visible', 'leaving');
        nowPlayingBanner.removeEventListener('transitionend', onOut);
        currentMode = 'hidden';
      };
      nowPlayingBanner.addEventListener('transitionend', onOut);
    }

    function applyPlayingContent(trackTitle) {
      if (nowPlayingPrefix) nowPlayingPrefix.textContent = '♪ Sonando ahora mismo:';
      if (nowPlayingTitle) nowPlayingTitle.textContent = trackTitle || '—';
      if (nowPlayingSuffix) nowPlayingSuffix.textContent = '♪';
    }

    function applyPausedHintContent() {
      if (nowPlayingPrefix) nowPlayingPrefix.textContent = '♪';
      if (nowPlayingTitle) nowPlayingTitle.textContent = 'Para una mejor experiencia, recuerda reproducir la música';
      if (nowPlayingSuffix) nowPlayingSuffix.textContent = '♪';
    }

    function enterFromLeft() {
      nowPlayingBanner.classList.remove('leaving');
      void nowPlayingBanner.offsetWidth;
      nowPlayingBanner.classList.add('visible');
    }

    function showNewMessage(applyContent, newMode) {
      clearPauseTimeout();
      const wasVisible = (currentMode === 'playing' || currentMode === 'paused');

      if (wasVisible) {
        // Fase 1: salir a la derecha
        nowPlayingBanner.classList.add('leaving');
        const onOut = () => {
          nowPlayingBanner.removeEventListener('transitionend', onOut);
          nowPlayingBanner.classList.remove('visible', 'leaving');
          currentMode = newMode;
          applyContent();
          requestAnimationFrame(() => enterFromLeft());
        };
        nowPlayingBanner.addEventListener('transitionend', onOut);
      } else {
        currentMode = newMode;
        applyContent();
        enterFromLeft();
      }

      if (newMode === 'paused') {
        pauseHintTimeout = setTimeout(() => {
          if (currentMode === 'paused') setBannerHidden();
        }, 8000);
      }
    }

    function setBannerPlaying(trackTitle) {
      showNewMessage(
        () => applyPlayingContent(trackTitle),
        'playing'
      );
    }

    function setBannerPausedHint() {
      showNewMessage(applyPausedHintContent, 'paused');
    }

    // Sincronizar icono y banner con el estado global del AudioManager
    document.addEventListener('audioStateChanged', function(e) {
      if (!e.detail) return;
      const volume = typeof e.detail.volume === 'number' ? e.detail.volume : 0.5;
      const trackInfo = e.detail.trackInfo;
      const isPlaying = !!e.detail.isPlaying;

      updateIcon(isPlaying);

      // Mostrar uno de los dos mensajes, nunca ambos
      if (isPlaying && trackInfo && trackInfo.title && volume > 0.001) {
        setBannerPlaying(trackInfo.title);
      } else if (volume > 0.001) {
        // Música pausada o detenida pero no silenciada → mostrar recomendación
        setBannerPausedHint();
      } else {
        // Silenciado manualmente → ocultar completamente el indicador
        setBannerHidden();
      }
    });

    // Toggle play/pause de la música de fondo
    toggleBtn.addEventListener('click', function() {
      if (!window.audioManager) return;
      window.audioManager.togglePlayPause();
    });

    // Inicializar icono cuando el AudioManager esté listo
    function initFromManager() {
      if (!window.audioManager) return;

      updateIcon(!!window.audioManager.isPlaying);
      const info = window.audioManager.getCurrentTrackInfo
        ? window.audioManager.getCurrentTrackInfo()
        : null;

      if (window.audioManager.currentVolume <= 0.001) {
        setBannerHidden();
      } else if (window.audioManager.isPlaying && info && info.title) {
        setBannerPlaying(info.title);
      } else {
        setBannerPausedHint();
      }
    }

    if (window.audioManager) {
      initFromManager();
    } else {
      document.addEventListener('audioManagerReady', initFromManager, { once: true });
    }
  })();
</script>
