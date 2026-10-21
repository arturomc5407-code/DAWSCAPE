/**
 * Validation SFX Helper - Simplifica la reproducción de sonidos en validaciones
 * Uso: triggerValidationSuccess() o triggerValidationError()
 */

/**
 * Reproducir SFX de validación correcta
 * Uso: triggerValidationSuccess();
 */
function triggerValidationSuccess() {
  if (window.audioManager) {
    window.audioManager.playSFX('correct');
  }
  document.dispatchEvent(new CustomEvent('validation:correct'));
}

/**
 * Reproducir SFX de validación incorrecta
 * Uso: triggerValidationError();
 */
function triggerValidationError() {
  if (window.audioManager) {
    window.audioManager.playSFX('error');
  }
  document.dispatchEvent(new CustomEvent('validation:error'));
}

/**
 * Wrapper para validaciones por fetch
 * Uso en respuesta AJAX de validación:
 * 
 * fetch('/prueba/validar', {...})
 *   .then(r => r.json())
 *   .then(data => {
 *     if (data.success) {
 *       playValidationSFX(true);
 *     } else {
 *       playValidationSFX(false);
 *     }
 *   });
 */
function playValidationSFX(success) {
  if (success) {
    triggerValidationSuccess();
  } else {
    triggerValidationError();
  }
}

/**
 * Reproducir SFX después de un delay
 * Útil para validaciones que requieren tiempo de espera
 */
function playValidationSFXDelayed(success, delayMs = 300) {
  setTimeout(() => {
    playValidationSFX(success);
  }, delayMs);
}
