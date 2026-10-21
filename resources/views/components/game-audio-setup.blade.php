{{-- Componente Game Audio Setup - Inicializa audio para las pruebas --}}
{{--
    Este componente funciona tanto en vistas standalone (sin layout)
    como en vistas que extienden layouts con @stack('scripts').
    Inyecta window.AUDIO_BASE_URL desde asset() para que el AudioManager
    pueda construir URLs correctas en cualquier entorno.
--}}
@props(['trackKey' => 'prueba1'])

<!-- Audio Setup -->
<div id="game-audio-setup" class="game-audio-setup" data-track="{{ $trackKey }}"></div>

{{-- Inyectar la base URL del audio para vistas que no usan el layout base --}}
<script>
    if (typeof window.AUDIO_BASE_URL === 'undefined') {
        window.AUDIO_BASE_URL = "{{ asset('sonido') }}";
    }
</script>

<script>
    (function () {
        const TRACK_KEY = '{{ $trackKey }}';

        /**
         * Intenta reproducir la pista con el AudioManager global.
         * Si el navegador bloquea el autoplay, registra el listener de
         * primer clic para arrancar la música en cuanto el usuario
         * interactúe con la página.
         */
        function initializeAudio() {
            if (!window.audioManager) {
                // AudioManager aún no disponible (Vite async), esperar al evento
                document.addEventListener('audioManagerReady', initializeAudio, { once: true });
                return;
            }

            console.log('✓ AudioManager listo. Reproduciendo:', TRACK_KEY);

            const track = window.audioManager.tracks[TRACK_KEY];
            if (!track) {
                console.warn('Track no definido en AudioManager:', TRACK_KEY);
                return;
            }

            // Asegurar que el audioManager usa la URL correcta en esta página
            if (!window.audioManager.baseUrl || window.audioManager.baseUrl === '/sonido') {
                window.audioManager.baseUrl = (window.AUDIO_BASE_URL || '/sonido').replace(/\/$/, '');
            }

            window.audioManager.bgMusic.src = window.audioManager._url(track.file);
            window.audioManager.bgMusic.loop = true;
            window.audioManager.bgMusic.volume = 0;
            window.audioManager.currentTrach = TRACK_KEY;

            window.audioManager.bgMusic.play()
                .then(() => {
                    window.audioManager.isPlaying = true;
                    window.audioManager._fadeInMusic(2000);
                    window.audioManager._updateUI();
                })
                .catch((err) => {
                    // Navegador bloqueó el autoplay (política de usuario)
                    console.warn('⚠ Autoplay bloqueado. Se activará con el primer clic.', err.message);
                    registerAutoplayUnlock();
                });
        }

        /**
         * Registra listeners de un solo uso para desbloquear el audio
         * en el primer gesto del usuario (click, toque o tecla).
         */
        function registerAutoplayUnlock() {
            showAutoplayHint();

            const eventos = ['click', 'touchstart', 'keydown'];

            function unlock() {
                if (!window.audioManager) return;

                // Reanudar AudioContext de WebAudio API (SFX de click)
                if (window.audioManager._audioCtx && window.audioManager._audioCtx.state === 'suspended') {
                    window.audioManager._audioCtx.resume().catch(() => {});
                }

                window.audioManager.bgMusic.play()
                    .then(() => {
                        window.audioManager.isPlaying = true;
                        window.audioManager._fadeInMusic(2000);
                        window.audioManager._updateUI();
                    })
                    .catch((e) => {
                        console.error('No se pudo reproducir tras interacción:', e);
                    });

                // Eliminar todos los listeners tras el primer gesto
                eventos.forEach((ev) => document.removeEventListener(ev, unlock, true));
                hideAutoplayHint();
            }

            eventos.forEach((ev) => document.addEventListener(ev, unlock, { once: false, capture: true }));
        }

        /**
         * Muestra un banner en la parte superior indicando que la música
         * arrancará al hacer clic, de acuerdo con las políticas de autoplay.
         */
        function showAutoplayHint() {
            if (document.getElementById('autoplay-hint')) return;

            const hint = document.createElement('div');
            hint.id = 'autoplay-hint';
            hint.innerHTML = '🔇 Haz clic en cualquier lugar para activar la música';
            hint.style.cssText = [
                'position:fixed',
                'top:0',
                'left:0',
                'right:0',
                'background:rgba(0,0,0,0.75)',
                'color:#00ff88',
                'font-family:"Share Tech Mono",monospace',
                'font-size:0.85rem',
                'text-align:center',
                'padding:0.5rem 1rem',
                'z-index:99999',
                'cursor:pointer',
                'letter-spacing:0.05em',
            ].join(';');

            document.body.appendChild(hint);
        }

        function hideAutoplayHint() {
            const hint = document.getElementById('autoplay-hint');
            if (hint) {
                hint.style.transition = 'opacity 0.5s';
                hint.style.opacity = '0';
                setTimeout(() => hint.remove(), 500);
            }
        }

        // Arrancar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeAudio);
        } else {
            initializeAudio();
        }
    })();
</script>

<!-- Music Player Component -->
@include('components.music-player')
