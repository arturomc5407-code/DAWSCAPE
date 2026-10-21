@php
    $partidaUuid = \App\Models\Partida::where('email', session('email'))->value('uuid');
@endphp

@if($partidaUuid)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.Echo) {
            
            // 1. Escuchar Eventos Globales (Botón del Pánico)
            window.Echo.channel('global-events')
                .listen('PanicToggled', (e) => {
                    if (e.isPaused) {
                        detenerJuegoGlobal();
                    } else {
                        reanudarJuegoGlobal();
                    }
                });

            // 2. Escuchar Eventos Privados (Mensajes del Admin)
            window.Echo.channel('equipo.{{ $partidaUuid }}')
                .listen('DirectMessage', (e) => {
                    mostrarMensajeDirecto(e.mensaje);
                });
                
        }
    });

    let panicOverlay = null;

    function detenerJuegoGlobal() {
        if (!panicOverlay) {
            panicOverlay = document.createElement('div');
            panicOverlay.id = 'panic-overlay';
            panicOverlay.style.cssText = `
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(153, 27, 27, 0.95); z-index: 999999;
                display: flex; flex-direction: column; align-items: center; justify-content: center;
                color: white; font-family: 'Share Tech Mono', monospace; text-align: center;
            `;
            panicOverlay.innerHTML = `
                <i class="ph-fill ph-warning-circle" style="font-size: 6rem; color: #fca5a5; margin-bottom: 2rem; animation: pulse 2s infinite;"></i>
                <h1 style="font-size: 3rem; margin: 0; text-transform: uppercase;">SISTEMA EN PAUSA</h1>
                <p style="font-size: 1.5rem; color: #fecaca; margin-top: 1rem;">El administrador ha detenido el tiempo globalmente.<br>Por favor, aguardad instrucciones.</p>
            `;
            document.body.appendChild(panicOverlay);
        }
        
        // Detener el cronómetro visual si existe la variable intervalo de timer JS
        if (typeof timerInterval !== 'undefined') {
            clearInterval(timerInterval);
        }
    }

    function reanudarJuegoGlobal() {
        if (panicOverlay) {
            panicOverlay.remove();
            panicOverlay = null;
        }
        // Recargar la página para que el backend recalcule los offset de tiempo con la nueva Base de Datos
        window.location.reload();
    }

    function mostrarMensajeDirecto(mensaje) {
        Swal.fire({
            icon: 'warning',
            title: 'MENSAJE DEL ADMINISTRADOR',
            text: mensaje,
            background: '#111827',
            color: '#fff',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'RECIBIDO'
        });
    }
</script>
<style>
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endif
