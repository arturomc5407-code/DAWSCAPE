{{-- 
  EJEMPLO: Integración de Sistema de Audio en Prueba
  Este archivo muestra cómo integrar el audio system completo en una prueba
  
  Para aprovechar este ejemplo en prueba1.blade.php, copia las líneas relevantes
  y adáptaslas según tu estructuradeexistente.
--}}

<!-- 
═══════════════════════════════════════════════════════════════════════════════
  1. INCLUIR COMPONENTE DE AUDIO AL INICIO (después del @extends)
═══════════════════════════════════════════════════════════════════════════════
-->

{{-- Este componente automáticamente:
     - Carga AudioManager
     - Incluye SFX Integration
     - Muestra el mini reproductor
     - Inicia música con fade-in
--}}
@include('components.game-audio-setup', ['trackKey' => 'prueba1'])




<!-- 
═══════════════════════════════════════════════════════════════════════════════
  2. SCRIPT HELPERS PARA VALIDACION (agregar en @push('scripts'))
═══════════════════════════════════════════════════════════════════════════════
-->

@push('scripts')
  @vite('resources/js/validation-sfx-helper.js')

  <script>
    /**
     * Ejemplo: Función de validación con SFX integrado
     * 
     * Esta función simula una validación, reproduce el SFX apropiado,
     * y muestra el feedback al usuario.
     */
    function validarPruebaConAudio(isCorrect) {
      if (isCorrect) {
        // Reproducir sonido de éxito
        triggerValidationSuccess();
        
        // Mostrar feedback
        showSuccessMessage('¡Correcto! Pasando a la siguiente prueba...');
        
        // Avanzar después de 1 segundo
        setTimeout(() => window.location.href = '/prueba2', 1000);
      } else {
        // Reproducir sonido de error
        triggerValidationError();
        
        // Mostrar feedback
        showErrorMessage('Intenta de nuevo...');
      }
    }

    /**
     * Ejemplo: Fetch con SFX automático
     */
    async function validarRespuestaFetch(answer) {
      try {
        const response = await fetch('/prueba1', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ answer: answer })
        });

        const data = await response.json();

        // El SFX se reproduce automáticamente basado en el resultado
        playValidationSFX(data.success);

        if (data.success) {
          showSuccessMessage('¡Correcto!');
          setTimeout(() => window.location.href = '/prueba2', 1500);
        } else {
          showErrorMessage('Respuesta incorrecta. Intenta nuevamente.');
        }
      } catch (error) {
        console.error('Error:', error);
        triggerValidationError();
      }
    }

    // Funciones auxiliares
    function showSuccessMessage(msg) {
      // Tu implementación de mostrar mensaje de éxito
      console.log('✓ ' + msg);
    }

    function showErrorMessage(msg) {
      // Tu implementación de mostrar mensaje de error
      console.log('✗ ' + msg);
    }
  </script>
@endpush




<!-- 
═════════════════════════════════════════════════════════════════════════════════
  3. EN UN BOTÓN DE VALIDACIÓN HTML
═════════════════════════════════════════════════════════════════════════════════
-->

{{-- Opción 1: Botón con onclick directo --}}
<button class="btn-validar" onclick="validarPruebaConAudio(true)">
  Validar Respuesta
</button>

{{-- Opción 2: Botón con listener en JavaScript --}}
<button id="btnValidar" class="btn-validar">
  Validar Respuesta
</button>

<script>
  document.getElementById('btnValidar').addEventListener('click', async () => {
    const answer = document.getElementById('answer').value;
    
    // Simular validación básica
    const isCorrect = answer === 'RESPUESTA_CORRECTA';
    
    // Reproducir SFX y feedback
    if (isCorrect) {
      triggerValidationSuccess();
      console.log('✓ Respuesta correcta');
    } else {
      triggerValidationError();
      console.log('✗ Respuesta incorrecta');
    }
  });
</script>




<!-- 
═════════════════════════════════════════════════════════════════════════════════
  4. CONTROL MANUAL DE MÚSICA (EJEMPLOS AVANZADOS)
═════════════════════════════════════════════════════════════════════════════════
-->

@push('scripts')
  <script>
    /**
     * Cambiar música durante la prueba
     * Por ejemplo, música de boss fight cuando se activa una fase especial
     */
    function activarFaseEspecial() {
      // El cambio automáticamente hace fade-out + fade-in
      audioManager.playMusic('prueba2', 1500);
    }

    /**
     * Pausar música con botón personalizado
     */
    document.getElementById('pauseBtn')?.addEventListener('click', () => {
      audioManager.togglePlayPause();
    });

    /**
     * Reducir volumen para que se escuche un anuncio o pista
     */
    function playPistaAnnouncement() {
      // Bajar volumen
      audioManager.setVolume(0.3);
      
      // Reproducir sonido de pista
      audioManager.playSFX('hint');
      
      // Volver al volumen normal después de 3 segundos
      setTimeout(() => {
        audioManager.setVolume(0.5);
      }, 3000);
    }

    /**
     * Pausar todo cuando se abre un modal
     */
    function abrirModal() {
      audioManager.pauseMusic();
      // ... resto del código del modal
    }

    function cerrarModal() {
      audioManager.resumeMusic();
      // ... resto del código
    }

    /**
     * Mute global
     */
    function muteAll() {
      audioManager.setVolume(0);
    }

    function unmuteAll() {
      audioManager.setVolume(0.5);
    }
  </script>
@endpush




<!-- 
═════════════════════════════════════════════════════════════════════════════════
  NOTAS DE INTEGRACIÓN
═════════════════════════════════════════════════════════════════════════════════

1. SIEMPRE incluir al inicio:
   @include('components.game-audio-setup', ['trackKey' => 'prueba1'])

2. Los tracks disponibles son:
   - 'login', 'prueba1' - 'prueba10', 'final'

3. Los SFX disponibles son:
   - 'correct' → Acierto.mp3
   - 'error' → Error.mp3
   - 'hint' → Boton-Pista.mp3

4. El botón de pista automáticamente reproduce sonido (ya integrado)

5. Para validaciones, usa los helpers:
   - triggerValidationSuccess()
   - triggerValidationError()
   - playValidationSFX(boolean)
   - playValidationSFXDelayed(boolean, ms)

6. audioManager está disponible globalmente como window.audioManager

7. El mini reproductor se muestra automáticamente en la esquina inferior izquierda

8. La música se persiste en volumen (localStorage)

═════════════════════════════════════════════════════════════════════════════════
--}}
