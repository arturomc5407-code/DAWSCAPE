// =====================================================
// PRUEBA 6 – SIMON SAYS COOPERATIVO (Auto-inicializable)
// Este módulo se auto-ejecuta al cargarse via @vite.
// Lee la configuración del DOM (data-attributes) y se
// conecta a los elementos HTML directamente.
// =====================================================

// ── WEB AUDIO API ──
let audioCtx;

function initAudio() {
    if (!audioCtx) {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        audioCtx = new AudioContext();
    }
    if (audioCtx.state === "suspended") {
        audioCtx.resume();
    }
}

function playTone(freq, type = "sine", duration = 0.3) {
    if (!audioCtx) return;
    const osc = audioCtx.createOscillator();
    const gainNode = audioCtx.createGain();
    osc.type = type;
    osc.frequency.value = freq;
    gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
    gainNode.gain.linearRampToValueAtTime(0.5, audioCtx.currentTime + 0.05);
    gainNode.gain.exponentialRampToValueAtTime(
        0.001,
        audioCtx.currentTime + duration,
    );
    osc.connect(gainNode);
    gainNode.connect(audioCtx.destination);
    osc.start();
    osc.stop(audioCtx.currentTime + duration);
}

// Frecuencias por sector
const FREQS = [329.63, 261.63, 220.0, 164.81];
const S_ERR = 110.0;
const S_OK = 880.0;

// ── SIMON GAME STATE ──
let sequence = [];
let playerStep = 0;
let roundLengths = [4, 6, 9, 12, 15];
let currentRound = 0;
let isPlaying = false;

// DOM refs
let statusTxt, rondaVal, pasosVal, board;

// Penalización local aplicada al cronómetro (en segundos)
let addLocalPenalty = null;

function lightUp(sectorId, isPlayer = false) {
    const btn = document.getElementById("btn-" + sectorId);
    if (!btn) return;
    btn.classList.add("lit");
    playTone(FREQS[sectorId], isPlayer ? "square" : "sine", 0.4);
    setTimeout(() => btn.classList.remove("lit"), 450);
}

function playRound() {
    isPlaying = false;
    if (board) board.classList.remove("active");
    playerStep = 0;

    if (rondaVal)
        rondaVal.innerText = currentRound + 1 + "/" + roundLengths.length;
    let targetSteps = roundLengths[currentRound];
    if (pasosVal) pasosVal.innerText = targetSteps;

    if (statusTxt) {
        statusTxt.innerText = "// MEMORIZANDO PATRÓN...";
        statusTxt.className = "status-info";
        statusTxt.style.color = "";
    }

    let i = 0;
    setTimeout(() => {
        let interval = setInterval(() => {
            lightUp(sequence[i]);
            i++;
            if (i >= targetSteps) {
                clearInterval(interval);
                setTimeout(() => {
                    if (statusTxt) {
                        statusTxt.innerText =
                            "INTRODUCE LA SECUENCIA REQUERIDA";
                        statusTxt.className = "";
                    }
                    isPlaying = true;
                    if (board) board.classList.add("active");
                }, 500);
            }
        }, 800);
    }, 1000);
}

function handleSectorTouch(sectorId, event) {
    // Prevenir comportamiento por defecto en móvil
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    if (!isPlaying) return;

    lightUp(sectorId, true);

    let targetSteps = roundLengths[currentRound];

    if (sectorId === sequence[playerStep]) {
        playerStep++;
        if (playerStep === targetSteps) {
            isPlaying = false;
            if (board) board.classList.remove("active");
            if (statusTxt) {
                statusTxt.innerText = "[OK] FASE ESTABILIZADA";
                statusTxt.className = "status-ok";
            }
            if (window.audioManager) window.audioManager.playSFX('correct');
            playTone(S_OK, "triangle", 0.5);

            setTimeout(() => {
                currentRound++;
                if (currentRound >= roundLengths.length) {
                    winGame();
                } else {
                    playRound();
                }
            }, 1500);
        }
    } else {
        isPlaying = false;
        if (board) board.classList.remove("active");
        if (board) board.style.animation = "shake 0.4s";
        if (statusTxt) {
            statusTxt.innerText =
                "[PENALIZACIÓN] Secuencia incorrecta. +30 segundos añadidos a tu tiempo. REINICIANDO FASE...";
            statusTxt.className = "status-info";
            statusTxt.style.color = "var(--red)";
        }
        if (window.audioManager) window.audioManager.playSFX('error');
        playTone(S_ERR, "sawtooth", 0.8);

        // Penalizar al equipo con 30 segundos
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            fetch("/prueba6/penalizar", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken.getAttribute("content"),
                },
            })
                .then((r) => r.json())
                .then((data) => {
                    if (data && data.success && typeof addLocalPenalty === "function") {
                        addLocalPenalty(data.penalizacion || 30);
                    }
                })
                .catch((err) => console.error("Error al penalizar:", err));
        }

        setTimeout(() => {
            if (board) board.style.animation = "";
            playRound();
        }, 3000); // Aumentado a 3 segundos para que lean el mensaje
    }
}

function winGame() {
    // Restaurar volumen de la música al 100%
    if (typeof window.prueba6RestoreVolume === 'function') {
        window.prueba6RestoreVolume();
    }

    document.getElementById("screen-game").style.display = "none";
    document.getElementById("screen-success").style.display = "flex";

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) return;

    fetch("/prueba6", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken.getAttribute("content"),
        },
        body: JSON.stringify({ completed: true, expected_sequence: sequence }),
    })
        .then((r) => r.json())
        .then((res) => {
            // Validación exitosa
        })
        .catch((err) => console.error("Error al validar:", err));
}

// =====================================================
// AUTO-INICIALIZACIÓN (se ejecuta cuando el módulo carga)
// =====================================================
document.addEventListener("DOMContentLoaded", function () {
    // ── TIMER ─
    const configEl = document.getElementById("prueba6-config");
    const timerDisplay = document.getElementById("cronometro");
    const MAX_MINUTOS = 90;

    if (configEl && timerDisplay) {
        let tiempoInicial = parseInt(configEl.dataset.tiempo || "0", 10);
        const loadTimeMs = Date.now();

        function actualizarTimer() {
            let tiempo =
                tiempoInicial + Math.floor((Date.now() - loadTimeMs) / 1000);
            let minutos = parseInt(tiempo / 60, 10);
            let segundos = parseInt(tiempo % 60, 10);
            let m = minutos < 10 ? "0" + minutos : minutos;
            let s = segundos < 10 ? "0" + segundos : segundos;
            if (timerDisplay) timerDisplay.textContent = m + ":" + s;
            if (minutos >= MAX_MINUTOS) {
                window.location.href = "/final";
                return;
            }
        }
        window.actualizarTimer = actualizarTimer;
        actualizarTimer();
        setInterval(actualizarTimer, 1000);
    }

    // ── GAME START ──
    const btnStart = document.getElementById("btn-start");
    if (btnStart) {
        btnStart.addEventListener("click", function () {
            initAudio();

            // Bajar volumen de la música al 50% durante la secuencia
            if (typeof window.prueba6ReduceVolume === 'function') {
                window.prueba6ReduceVolume();
            }

            btnStart.style.display = "none";
            const hudContainer = document.getElementById("hud-container");
            if (hudContainer) hudContainer.style.display = "flex";

            // Leer la secuencia del backend desde el data-attribute
            const configEl = document.getElementById("prueba6-config");
            const initialSequence = configEl
                ? JSON.parse(configEl.dataset.sequence || "[]")
                : [];

            // Init DOM refs
            statusTxt = document.getElementById("sys-status");
            rondaVal = document.getElementById("ronda-val");
            pasosVal = document.getElementById("pasos-val");
            board = document.getElementById("board");

            // Init game state
            sequence = initialSequence;
            let maxSteps = sequence.length;
            if (maxSteps > 0) {
                roundLengths = [4, 6, 9, 12, maxSteps];
            }
            currentRound = 0;

            playRound();
        });
    }

    // ── SECTOR TOUCH (registrar en window para los onclick inline del HTML) ──
    window.touchSector = handleSectorTouch;

    // ── EVENT LISTENERS PARA MÓVIL Y ORDENADOR ──
    for (let i = 0; i < 4; i++) {
        const btn = document.getElementById("btn-" + i);
        if (btn) {
            // Prevenir comportamiento por defecto
            btn.addEventListener("touchstart", function (e) {
                e.preventDefault();
                handleSectorTouch(i, e);
            });
            btn.addEventListener("mousedown", function (e) {
                e.preventDefault();
                handleSectorTouch(i, e);
            });
            btn.style.touchAction = "none";
            btn.style.userSelect = "none";
        }
    }
});
