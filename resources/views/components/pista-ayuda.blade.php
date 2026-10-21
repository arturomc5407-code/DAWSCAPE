{{--
  Componente Pista/Ayuda DAWSCAPE — Sistema de diálogo estilo Pokémon
  Props: $pruebaNumero, $pistaConfig, $yaUsada
--}}
@php
    $imagenCara = $pistaConfig['imagen'] ?? 'mario.png';
    $imagenHabla = $pistaConfig['imagen_habla'] ?? null;
    $creador    = $pistaConfig['creador'] ?? 'Ayudante';
    $pruebaNum  = $pruebaNumero ?? 1;
    $usada      = $yaUsada ?? false;
@endphp

<style>
/* ═══ SISTEMA DE PISTAS — DIÁLOGO POKÉMON ═══ */

/* --- Botón flotante inferior derecho --- */
.pista-btn {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 4px solid var(--green, #00ff88);
    background: linear-gradient(135deg, rgba(4, 26, 14, 0.95), rgba(0, 50, 25, 0.95));
    cursor: pointer;
    z-index: 500;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 0 20px rgba(0,255,136,0.5), 0 6px 20px rgba(0,0,0,0.6), inset 0 0 15px rgba(0,255,136,0.2);
    animation: pista-pulse 2s ease-in-out infinite;
    padding: 0;
}
.pista-btn img {
    width: 100%; height: 100%;
    object-fit: cover;
    image-rendering: pixelated;
    display: block;
}
.pista-btn:hover {
    transform: scale(1.15);
    box-shadow: 0 0 25px rgba(0,255,136,0.5), 0 0 50px rgba(0,255,136,0.2);
    border-color: #66ffaa;
}
.pista-btn:active { transform: scale(1.05); }
.pista-btn.usada {
    border-color: #ffaa00;
    box-shadow: 0 0 15px rgba(255,170,0,0.3), 0 4px 15px rgba(0,0,0,0.5);
    animation: none;
}
.pista-btn.usada:hover { box-shadow: 0 0 25px rgba(255,170,0,0.5); border-color: #ffcc44; }

@keyframes pista-pulse {
    0%,100% { box-shadow: 0 0 15px rgba(0,255,136,0.3), 0 4px 15px rgba(0,0,0,0.5); }
    50%     { box-shadow: 0 0 25px rgba(0,255,136,0.5), 0 0 40px rgba(0,255,136,0.15); }
}

/* --- Tooltip hover --- */
.pista-tooltip {
    position: fixed;
    bottom: 6rem; right: 1.2rem;
    background: rgba(4,26,14,0.95);
    border: 1px solid var(--green, #00ff88);
    color: var(--green, #00ff88);
    font-family: 'Share Tech Mono', monospace;
    font-size: 0.7rem;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    white-space: nowrap;
    z-index: 499;
    pointer-events: none;
    opacity: 0;
    transform: translateY(5px);
    transition: all 0.3s ease;
}

/* --- Overlay oscuro --- */
.pista-dialog-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.7);
    z-index: 600;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    backdrop-filter: blur(4px);
}
.pista-dialog-overlay.visible {
    opacity: 1;
    pointer-events: auto;
}

/* --- Personaje arriba --- */
.pista-character-area {
    margin-top: 3rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    transform: translateY(-30px);
    opacity: 0;
    transition: all 0.4s ease 0.1s;
}
.pista-dialog-overlay.visible .pista-character-area {
    transform: translateY(0);
    opacity: 1;
}
.pista-character-frame {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid var(--green, #00ff88);
    overflow: hidden;
    background: linear-gradient(135deg, #041a0e, #0a2f18);
    box-shadow: 0 0 25px rgba(0,255,136,0.4), 0 8px 25px rgba(0,0,0,0.6);
    position: relative;
}
.pista-character-frame img {
    width: 100%; height: 100%;
    object-fit: cover;
    image-rendering: pixelated;
    display: block;
}
/* Indicador visual de que está hablando (borde animado) */
.pista-character-frame.talking {
    border-color: #66ffaa;
    box-shadow: 0 0 30px rgba(0,255,136,0.6), 0 8px 25px rgba(0,0,0,0.6);
}
.pista-character-name {
    margin-top: 0.5rem;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.85rem;
    color: var(--green, #00ff88);
    text-shadow: 0 0 10px rgba(0,255,136,0.5);
    letter-spacing: 0.1em;
}

/* --- Bocadillo de diálogo --- */
.pista-speech-container {
    margin-top: 1.2rem;
    width: 90%;
    max-width: 520px;
    transform: scale(0.85) translateY(15px);
    opacity: 0;
    transition: all 0.4s ease 0.25s;
}
.pista-dialog-overlay.visible .pista-speech-container {
    transform: scale(1) translateY(0);
    opacity: 1;
}
.pista-speech-box {
    position: relative;
    background: linear-gradient(180deg, #f8f5e8 0%, #e8e4d4 100%);
    border: 4px solid #2a2a3e;
    border-radius: 14px;
    padding: 1.5rem 1.3rem 1.3rem;
    box-shadow: 0 4px 30px rgba(0,0,0,0.5), inset 0 2px 0 rgba(255,255,255,0.3);
    min-height: 120px;
}
/* Punta del bocadillo apuntando arriba */
.pista-speech-box::before {
    content: '';
    position: absolute;
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
    width: 0; height: 0;
    border-left: 14px solid transparent;
    border-right: 14px solid transparent;
    border-bottom: 14px solid #2a2a3e;
}
.pista-speech-box::after {
    content: '';
    position: absolute;
    top: -9px;
    left: 50%;
    transform: translateX(-50%);
    width: 0; height: 0;
    border-left: 11px solid transparent;
    border-right: 11px solid transparent;
    border-bottom: 11px solid #f8f5e8;
}

.pista-speech-text {
    font-family: 'Share Tech Mono', monospace;
    font-size: clamp(0.85rem, 2.8vw, 1rem);
    color: #1a1a2e;
    line-height: 1.8;
    min-height: 60px;
    word-wrap: break-word;
}
/* Cursor parpadeante al final del texto */
.pista-speech-text .pista-cursor {
    display: inline-block;
    width: 8px;
    height: 1.1em;
    background: #1a1a2e;
    margin-left: 2px;
    vertical-align: text-bottom;
    animation: pista-blink 0.6s steps(2) infinite;
}
@keyframes pista-blink {
    0%   { opacity: 1; }
    50%  { opacity: 0; }
    100% { opacity: 1; }
}

/* --- Botones Sí / No --- */
.pista-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 1.2rem;
    opacity: 0;
    transform: translateY(8px);
    transition: all 0.3s ease;
    pointer-events: none;
}
.pista-buttons.visible {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}
.pista-btn-si, .pista-btn-no {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.9rem;
    font-weight: 700;
    padding: 0.7rem 2.2rem;
    border-radius: 8px;
    border: 3px solid;
    cursor: pointer;
    transition: all 0.2s ease;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.pista-btn-si {
    background: linear-gradient(135deg, #00cc66, #009944);
    border-color: #00ff88;
    color: #fff;
    box-shadow: 0 0 15px rgba(0,255,136,0.3);
}
.pista-btn-si:hover {
    background: linear-gradient(135deg, #00ee77, #00bb55);
    box-shadow: 0 0 25px rgba(0,255,136,0.5);
    transform: scale(1.05);
}
.pista-btn-no {
    background: linear-gradient(135deg, #cc3344, #992233);
    border-color: #ff4466;
    color: #fff;
    box-shadow: 0 0 15px rgba(255,34,68,0.3);
}
.pista-btn-no:hover {
    background: linear-gradient(135deg, #ee4455, #bb3344);
    box-shadow: 0 0 25px rgba(255,34,68,0.5);
    transform: scale(1.05);
}

/* --- Botón cerrar --- */
.pista-close-dialog {
    position: absolute;
    top: -12px; right: -12px;
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 2px solid #2a2a3e;
    background: #ff4466;
    color: #fff;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center; justify-content: center;
    transition: all 0.2s;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    opacity: 0;
    pointer-events: none;
}
.pista-close-dialog.visible {
    opacity: 1;
    pointer-events: auto;
}
.pista-close-dialog:hover {
    background: #ff2244;
    transform: scale(1.1);
}

/* --- Aviso penalización --- */
.pista-penalty-banner {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.95);
    background: linear-gradient(135deg, rgba(255,170,0,0.95), rgba(255,140,0,0.95));
    color: #1a1a00;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.85rem; font-weight: 700;
    padding: 0.7rem 1.5rem;
    border-radius: 8px;
    z-index: 9999;
    opacity: 0;
    transition: all 0.4s ease;
    pointer-events: none;
    box-shadow: 0 4px 20px rgba(255,170,0,0.4);
    letter-spacing: 0.05em;
    text-align: center;
    max-width: 90vw;
    box-sizing: border-box;
}
.pista-penalty-banner.show {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

@media (max-width: 768px) {
    .pista-penalty-banner {
        font-size: 0.75rem;
        padding: 0.5rem 1rem;
        max-width: 92vw;
        width: auto;
        white-space: normal;
        word-break: break-word;
    }
}

@media (max-width: 600px) {
    .pista-btn { width: 58px; height: 58px; bottom: 1rem; right: 1rem; }
    .pista-tooltip { bottom: 5rem; right: 0.8rem; font-size: 0.65rem; }
    .pista-character-frame { width: 90px; height: 90px; }
    .pista-character-area { margin-top: 1.5rem; }
    .pista-speech-container { width: 95%; }
    .pista-speech-box { padding: 1.2rem 1rem 1rem; min-height: 100px; }
    .pista-speech-text { font-size: 0.82rem; }
    .pista-btn-si, .pista-btn-no { padding: 0.6rem 1.6rem; font-size: 0.8rem; }
    .pista-penalty-banner {
        font-size: 0.7rem;
        padding: 0.45rem 0.8rem;
    }
}
</style>

<!-- Botón flotante -->
<button class="pista-btn {{ $usada ? 'usada' : '' }}" id="pistaBtn" title="Pedir pista">
    <img src="/images/pistas/{{ $imagenCara }}" alt="Ayuda">
</button>
<div class="pista-tooltip" id="pistaTooltip">{{ $usada ? '📖 Ver pista' : '❓ ¿Necesitas ayuda?' }}</div>

<!-- Overlay diálogo completo -->
<div class="pista-dialog-overlay" id="pistaDialogOverlay">
    <!-- Personaje arriba -->
    <div class="pista-character-area">
        <div class="pista-character-frame" id="pistaCharFrame">
            <img src="/images/pistas/{{ $imagenCara }}" alt="{{ $creador }}" id="pistaCharImg"
                 data-img-closed="/images/pistas/{{ $imagenCara }}"
                 data-img-open="/images/pistas/{{ $imagenHabla ?? $imagenCara }}">
        </div>
        <div class="pista-character-name">{{ $creador }}</div>
    </div>

    <!-- Bocadillo de diálogo -->
    <div class="pista-speech-container">
        <div class="pista-speech-box" id="pistaSpeechBox">
            <div class="pista-speech-text" id="pistaSpeechText"></div>
            <div class="pista-buttons" id="pistaButtons">
                <button class="pista-btn-si" id="pistaBtnSi">SÍ</button>
                <button class="pista-btn-no" id="pistaBtnNo">NO</button>
            </div>
            <button class="pista-close-dialog" id="pistaCloseDialog" title="Cerrar">✕</button>
        </div>
    </div>
</div>

<!-- Banner penalización -->
<div class="pista-penalty-banner" id="pistaPenaltyBanner">⚠️ +8 MINUTOS de penalización añadidos al contador</div>

<script>
(function() {
    // ═══ CONFIG ═══
    var PRUEBA_NUMERO = {{ $pruebaNum }};
    var PENALIZACION_SEGUNDOS = 480; // 8 minutos
    var VELOCIDAD_TEXTO = 35; // ms por carácter
    var MOUTH_INTERVAL_MS = 150; // velocidad alternancia boca (natural ~120-180ms)
    var pistaYaUsada = {{ $usada ? 'true' : 'false' }};
    var pistaTextoCache = null;
    var fetchEnCurso = false;
    var typewriterInterval = null;
    var speakingAudio = null;
    var mouthAnimInterval = null;
    var dialogoActivo = false;
    var mouthOpen = false;

    // ═══ ELEMENTOS DOM ═══
    var btn = document.getElementById('pistaBtn');
    var tooltipEl = document.getElementById('pistaTooltip');
    var overlay = document.getElementById('pistaDialogOverlay');
    var charFrame = document.getElementById('pistaCharFrame');
    var charImg = document.getElementById('pistaCharImg');
    var speechText = document.getElementById('pistaSpeechText');
    var buttonsDiv = document.getElementById('pistaButtons');
    var btnSi = document.getElementById('pistaBtnSi');
    var btnNo = document.getElementById('pistaBtnNo');
    var closeBtn = document.getElementById('pistaCloseDialog');
    var bannerEl = document.getElementById('pistaPenaltyBanner');

    if (!btn || !overlay || !charImg) return;

    // ═══ IMÁGENES DE BOCA ═══
    var imgClosed = charImg.getAttribute('data-img-closed');
    var imgOpen = charImg.getAttribute('data-img-open');
    var hasSpeakingFrames = imgClosed !== imgOpen;

    // Precargar imagen de boca abierta para evitar parpadeos
    if (hasSpeakingFrames) {
        var preload = new Image();
        preload.src = imgOpen;
    }

    // ═══ ANIMACIÓN DE BOCA (alternancia de imágenes) ═══
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
        if (mouthAnimInterval) {
            clearInterval(mouthAnimInterval);
            mouthAnimInterval = null;
        }
        mouthOpen = false;
        if (hasSpeakingFrames) {
            charImg.src = imgClosed;
        }
    }

    // ═══ SONIDO DE HABLA ═══
    var _speakingAudioUrl = ((window.AUDIO_BASE_URL || '/sonido').replace(/\/$/, '')) + '/hablando-pista.mp3';

    function iniciarSonidoHabla() {
        try {
            if (speakingAudio) { speakingAudio.pause(); }
            speakingAudio = new Audio(_speakingAudioUrl);
            speakingAudio.loop = true;
            // Volumen alto para que destaque claramente sobre la música reducida
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

    // ═══ TYPEWRITER (estilo Pokémon) ═══
    function typewriter(texto, callback) {
        // Limpiar anterior
        if (typewriterInterval) { clearInterval(typewriterInterval); }
        speechText.innerHTML = '';
        var i = 0;

        iniciarSonidoHabla();
        iniciarAnimBoca();

        // Añadir cursor
        var cursorSpan = document.createElement('span');
        cursorSpan.className = 'pista-cursor';

        typewriterInterval = setInterval(function() {
            if (i < texto.length) {
                // Insertar carácter antes del cursor
                speechText.textContent = texto.substring(0, i + 1);
                speechText.appendChild(cursorSpan);
                i++;
            } else {
                clearInterval(typewriterInterval);
                typewriterInterval = null;
                detenerSonidoHabla();
                detenerAnimBoca();
                // Quitar cursor después de un momento
                setTimeout(function() {
                    if (cursorSpan.parentNode) cursorSpan.remove();
                }, 600);
                if (callback) callback();
            }
        }, VELOCIDAD_TEXTO);
    }

    // ═══ PAUSA/REANUDA MÚSICA AL ABRIR/CERRAR PISTA ═══

    function pausarMusica() {
        if (!window.audioManager) return;
        try {
            window.audioManager.pauseMusic();
        } catch(e) {
            console.warn('Error pausando música:', e);
        }
    }

    function reanudarMusica() {
        if (!window.audioManager) return;
        try {
            window.audioManager.resumeMusic();
        } catch(e) {
            console.warn('Error reanudando música:', e);
        }
    }

    // ═══ MOSTRAR / CERRAR DIÁLOGO ═══
    function abrirDialogo() {
        dialogoActivo = true;
        overlay.classList.add('visible');
        buttonsDiv.classList.remove('visible');
        closeBtn.classList.remove('visible');
        speechText.innerHTML = '';
        pausarMusica();
    }

    function cerrarDialogo() {
        dialogoActivo = false;
        overlay.classList.remove('visible');
        buttonsDiv.classList.remove('visible');
        closeBtn.classList.remove('visible');
        if (typewriterInterval) { clearInterval(typewriterInterval); typewriterInterval = null; }
        detenerSonidoHabla();
        detenerAnimBoca();
        reanudarMusica();
    }

    function mostrarBotones() {
        buttonsDiv.classList.add('visible');
    }

    function ocultarBotones() {
        buttonsDiv.classList.remove('visible');
    }

    function mostrarCerrar() {
        closeBtn.classList.add('visible');
    }

    function mostrarBanner() {
        bannerEl.classList.add('show');
        setTimeout(function() { bannerEl.classList.remove('show'); }, 5000);
    }

    function marcarComoUsada() {
        pistaYaUsada = true;
        btn.classList.add('usada');
        if (tooltipEl) tooltipEl.textContent = '📖 Ver pista';
    }

    // ═══ FLUJO: CONFIRMACIÓN → PISTA ═══
    function mostrarConfirmacion() {
        abrirDialogo();
        // Reproducir sonido del botón
        if (typeof audioManager !== 'undefined') {
            audioManager.playSFX('hint');
        }

        typewriter('¿Estás seguro de que quieres usar una pista? ⚠️ Usar una pista añadirá 8 minutos al contador del juego.', function() {
            mostrarBotones();
        });
    }

    function manejarNo() {
        ocultarBotones();
        typewriter('No te preocupes, ¡intenta resolverlo tú primero! Confío en que puedes hacerlo.', function() {
            mostrarCerrar();
            // Auto-cerrar después de 3s si no pulsa cerrar
            setTimeout(function() {
                if (dialogoActivo && !typewriterInterval) cerrarDialogo();
            }, 3000);
        });
    }

    function manejarSi() {
        ocultarBotones();

        // CASO ESPECIAL: Prueba 6 (Simon Dice) — no penalizar, mensaje gracioso
        if (PRUEBA_NUMERO === 6) {
            typewriter('Oye... solo tienes que memorizar o apuntar los colores... no tiene sentido pedirme una pista aquí, anda...', function() {
                mostrarCerrar();
                setTimeout(function() {
                    if (dialogoActivo && !typewriterInterval) cerrarDialogo();
                }, 4000);
            });
            return;
        }

        // Feedback inmediato antes del fetch
        typewriter('Déjame pensar... Un momento...', function() {
            solicitarPistaAlServidor();
        });
    }

    function solicitarPistaAlServidor() {
        if (fetchEnCurso) return;
        fetchEnCurso = true;

        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) {
            speechText.textContent = 'Error: falta el token CSRF.';
            mostrarCerrar();
            fetchEnCurso = false;
            return;
        }

        fetch('/pista', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfMeta.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ prueba_numero: PRUEBA_NUMERO })
        })
        .then(function(res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function(data) {
            fetchEnCurso = false;

            if (!data.success) {
                speechText.textContent = 'Error al obtener la pista.';
                mostrarCerrar();
                return;
            }

            pistaTextoCache = data.texto;

            // Aplicar penalización si es primera vez
            if (!data.ya_usada && data.penalizacion > 0) {
                // Actualizar timer local inmediatamente
                if (typeof window.tiempoInicial !== 'undefined') {
                    window.tiempoInicial += data.penalizacion;
                } else if (typeof tiempoInicial !== 'undefined') {
                    tiempoInicial += data.penalizacion;
                }
                // Forzar actualización visual del timer
                if (typeof window.actualizarTimer === 'function') {
                    window.actualizarTimer();
                } else if (typeof actualizarTimer === 'function') {
                    actualizarTimer();
                }
                mostrarBanner();
                marcarComoUsada();
            } else {
                marcarComoUsada();
            }

            // Reproducir sonido de pista
            if (typeof audioManager !== 'undefined') {
                audioManager.playSFX('hint');
            }

            // Mostrar la pista con animación
            typewriter(data.texto, function() {
                mostrarCerrar();
            });
        })
        .catch(function(err) {
            fetchEnCurso = false;
            speechText.textContent = 'Error de conexión. Inténtalo de nuevo.';
            mostrarCerrar();
        });
    }

    // ═══ MOSTRAR PISTA CACHEADA (ya usada anteriormente) ═══
    function mostrarPistaCacheada() {
        abrirDialogo();
        if (typeof audioManager !== 'undefined') {
            audioManager.playSFX('hint');
        }
        typewriter(pistaTextoCache, function() {
            mostrarCerrar();
        });
    }

    // ═══ EVENT LISTENERS ═══

    // Hover tooltip
    btn.addEventListener('mouseenter', function() {
        if (tooltipEl) { tooltipEl.style.opacity = '1'; tooltipEl.style.transform = 'translateY(0)'; }
    });
    btn.addEventListener('mouseleave', function() {
        if (tooltipEl) { tooltipEl.style.opacity = '0'; tooltipEl.style.transform = 'translateY(5px)'; }
    });

    // Click botón principal
    btn.addEventListener('click', function() {
        if (dialogoActivo) { cerrarDialogo(); return; }

        // Si ya usó la pista y tenemos cache, mostrar directamente sin confirmación
        if (pistaYaUsada && pistaTextoCache) {
            mostrarPistaCacheada();
            return;
        }

        // Primera vez: mostrar confirmación
        mostrarConfirmacion();
    });

    // Botones Sí / No
    btnSi.addEventListener('click', function(e) { e.stopPropagation(); manejarSi(); });
    btnNo.addEventListener('click', function(e) { e.stopPropagation(); manejarNo(); });

    // Cerrar
    closeBtn.addEventListener('click', function(e) { e.stopPropagation(); cerrarDialogo(); });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && dialogoActivo) cerrarDialogo();
    });
})();
</script>
