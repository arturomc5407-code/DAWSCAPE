/**
 * SFX Integration - Efectos de sonido para pruebas del juego
 * Maneja la reproducción automática de SFX en interacciones clave.
 *
 * Usa click + touchstart para máxima compatibilidad con
 * iOS Safari, Android Chrome y navegadores de escritorio.
 */
(function() {
  // Marca para evitar duplicar listeners globales de validación
  var _validationListenersReady = false;

  /**
   * Reproduce SFX evitando disparar el sonido dos veces en un mismo
   * gesto (touchstart + click generan ambos en móvil).
   */
  function playSFXOnce(sfxKey, element) {
    // Debounce: ignorar si ya se reprodujo hace <300 ms en este elemento
    var now = Date.now();
    var last = element._lastSfx || 0;
    if (now - last < 300) return;
    element._lastSfx = now;
    if (window.audioManager) window.audioManager.playSFX(sfxKey);
  }

  /**
   * Adjunta listeners de SFX a un elemento si aún no los tiene.
   */
  function attachSFXListener(element, sfxKey) {
    if (element._sfxAttached) return;
    element._sfxAttached = true;

    var handler = function(e) {
      if (element.disabled) return;
      playSFXOnce(sfxKey, element);
    };
    element.addEventListener('click', handler);
    element.addEventListener('touchstart', handler, { passive: true });
  }

  /**
   * Adjuntar listeners de SFX a elementos del DOM
   */
  function initSFXButtons() {
    // Botón de pista
    var pistaBtn = document.getElementById('pistaBtn');
    if (pistaBtn) {
      attachSFXListener(pistaBtn, 'hint');
    }

    // Listeners globales de validación (solo una vez)
    if (!_validationListenersReady) {
      _validationListenersReady = true;

      document.addEventListener('validation:correct', function() {
        if (window.audioManager) window.audioManager.playSFX('correct');
      });

      document.addEventListener('validation:error', function() {
        if (window.audioManager) window.audioManager.playSFX('error');
      });
    }

    // Buscar elementos con data-sfx
    document.querySelectorAll('[data-sfx]').forEach(function(element) {
      var sfxType = element.dataset.sfx;
      if (sfxType === 'check') {
        // El sonido se reproducirá según la validación (correct/error)
        return;
      }
      attachSFXListener(element, sfxType);
    });
  }

  /**
   * Enviar evento de validación
   */
  window.triggerValidationSuccess = function() {
    document.dispatchEvent(new CustomEvent('validation:correct'));
  };

  window.triggerValidationError = function() {
    document.dispatchEvent(new CustomEvent('validation:error'));
  };

  // Inicializar cuando el DOM esté listo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSFXButtons);
  } else {
    initSFXButtons();
  }

  // Re-inicializar cuando se cargue dinámicamente contenido.
  // Solo procesa los nodos nuevos para no duplicar listeners.
  var observer = new MutationObserver(function(mutations) {
    var hasNewNodes = false;
    for (var i = 0; i < mutations.length; i++) {
      if (mutations[i].addedNodes.length > 0) { hasNewNodes = true; break; }
    }
    if (hasNewNodes) initSFXButtons();
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
    attributes: false
  });
})();
