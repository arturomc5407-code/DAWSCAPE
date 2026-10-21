/**
 * AudioManager - Sistema completo de gestión de audio
 * Maneja música de fondo, efectos de sonido y fade in/out
 *
 * Usa window.AUDIO_BASE_URL (inyectado por Blade vía asset()) para
 * construir URLs correctas independientemente del subpath del servidor.
 */
class AudioManager {
  constructor() {
    // Detección de dispositivo móvil (iOS/Android ignoran audio.volume por JS)
    this.isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent)
      || (window.matchMedia && window.matchMedia('(pointer: coarse)').matches
          && window.matchMedia('(max-width: 1024px)').matches);

    // Música de fondo
    this.bgMusic = new Audio();
    this.bgMusic.crossOrigin = 'anonymous';
    this.bgMusic.loop = true;
    this.currentTrach = null;
    this.isPlaying = false;
    this.isFading = false;

    // Restaurar volumen guardado o usar 0.5 por defecto
    const savedVol = parseFloat(localStorage.getItem('audioVolume'));
    const initialVol = (!isNaN(savedVol) && savedVol >= 0 && savedVol <= 1) ? savedVol : 0.5;
    // En móvil, forzar volumen a 1 (el SO controla el volumen real)
    this.currentVolume = this.isMobile ? 1 : initialVol;
    this.targetVolume = this.isMobile ? 1 : initialVol;

    // Efectos de sonido (proporción respecto al master)
    this.sfx = {};
    this.sfxRatio = 0.85;

    // Audio ducking (para bajar el volumen cuando hay diálogos/pistas)
    this._isDucking = false;
    this._volumeBeforeDuck = null;

    // Base URL resuelta por Laravel asset() desde el layout.
    // Fallback a '/sonido' si la variable no fue inyectada.
    this.baseUrl = (window.AUDIO_BASE_URL || '/sonido').replace(/\/$/, '');

    // Configuración de pistas (rutas relativas a baseUrl)
    this.tracks = {
      login: {
        file: 'MusicaMenuLogin.mp3',
        title: 'DAWSCAPE - Tema Principal',
      },
      prueba1: {
        file: 'Prueba1-suspiro.mp3',
        title: 'Suspiro 8 Bits - Sergio Mouliaá',
      },
      prueba2: {
        file: 'Prueba2-zasi.mp3',
        title: 'X TUS BESOS 8 Bits - Zasi',
      },
      prueba3: {
        file: 'Prueba3-farsante.mp3',
        title: 'El Farsante 8 Bits - Ozuna',
      },
      prueba4: {
        file: 'Prueba4-Arturo.mp3',
        title: 'Pluma Pluma G.. 8 Bits - Morancos',
      },
      prueba5: {
        file: 'Prueba5-delocos.mp3',
        title: 'De Locos 8 Bits - Sergio Mouliaá',
      },
      prueba6: {
        file: 'Prueba6-fernanfloo.mp3',
        title: 'Rap de Fernanfloo 8 Bits - Bambiel',
      },
      prueba7: {
        file: 'Prueba7-NachoRPG.mp3',
        title: 'Tokyo Drift 8 Bits - Nacho',
      },
      prueba8: {
        file: 'Prueba8-mandanga.mp3',
        title: 'Mandanga Style 8 Bits - Amador',
      },
      prueba9: {
        file: 'Prueba9-MineroRPG.mp3',
        title: 'Minero 8 Bits - ElRubiusOMG',
      },
      prueba10: {
        file: 'Prueba10-DanzaKuduroRPG.mp3',
        title: 'Danza Kuduro 8 Bits - Fernando',
      },
      final: {
        file: 'FinalVictoria.mp3',
        title: 'We Are The Champions 8 Bits',
      },
    };

    // SFX (rutas relativas a baseUrl)
    // Nota: 'speaking' (hablando-pista.mp3) NO va aquí — lo gestiona
    // pista-ayuda.blade.php con su propio Audio() porque necesita loop.
    this.sfxFiles = {
      correct: 'Acierto.mp3',
      error: 'Error.mp3',
      hint: 'Boton-Pista.mp3',
      click: 'minecraft_click.mp3',
    };

    // Web Audio API context para SFX de baja latencia (click)
    this._audioCtx = null;
    this._clickBuffer = null;
    this._userHasInteracted = false;
    this._initWebAudioClick();

    this.initializeSFX();
    this.setupEventListeners();
    this._setupUserGestureUnlock();
  }

  /**
   * Construye la URL completa de un archivo de audio.
   * @param {string} filename - Nombre del archivo (ej: 'Acierto.mp3')
   * @returns {string}
   */
  _url(filename) {
    return this.baseUrl + '/' + filename;
  }

  /**
   * Precarga el sonido de click usando Web Audio API para latencia mínima.
   * En iOS Safari el AudioContext arranca en estado 'suspended'
   * y se reanuda en _setupUserGestureUnlock() con el primer gesto.
   */
  _initWebAudioClick() {
    try {
      const AudioContext = window.AudioContext || window.webkitAudioContext;
      if (!AudioContext) return;
      this._audioCtx = new AudioContext();

      this._loadClickBuffer();
    } catch(e) {}
  }

  /**
   * Descarga y decodifica el buffer del click.
   * Se llama tanto en init como tras reanudar el AudioContext.
   */
  _loadClickBuffer() {
    if (!this._audioCtx) return;
    fetch(this._url('minecraft_click.mp3'))
      .then(r => r.arrayBuffer())
      .then(buf => this._audioCtx.decodeAudioData(buf))
      .then(decoded => { this._clickBuffer = decoded; })
      .catch(() => {});
  }

  /**
   * Reproduce el click de minecraft con latencia mínima vía Web Audio API.
   * Cada llamada crea un BufferSource nuevo, así que múltiples clicks
   * rápidos se solapan sin cortarse.
   */
  _playClickWebAudio() {
    if (!this._audioCtx || !this._clickBuffer) return false;
    // Reanudar contexto si está suspendido (política de autoplay)
    if (this._audioCtx.state === 'suspended') {
      this._audioCtx.resume().catch(() => {});
      return false; // No intentar reproducir hasta que se reanude
    }
    try {
      const source = this._audioCtx.createBufferSource();
      const gainNode = this._audioCtx.createGain();
      source.buffer = this._clickBuffer;
      gainNode.gain.value = this.targetVolume * this.sfxRatio;
      source.connect(gainNode);
      gainNode.connect(this._audioCtx.destination);
      source.start(0);
      return true;
    } catch(e) {
      return false;
    }
  }

  /**
   * Registra listeners de primer gesto del usuario (click/touchstart/keydown)
   * para desbloquear el AudioContext y pre-inicializar el audio en iOS Safari.
   * Sin este paso, iOS no permite ninguna reproducción de audio.
   */
  _setupUserGestureUnlock() {
    const self = this;
    const eventos = ['click', 'touchstart', 'keydown'];

    function onFirstGesture() {
      if (self._userHasInteracted) return;
      self._userHasInteracted = true;

      // Reanudar AudioContext si está suspendido (iOS Safari)
      if (self._audioCtx && self._audioCtx.state === 'suspended') {
        self._audioCtx.resume().then(() => {
          // Re-cargar buffer si no se cargó antes
          if (!self._clickBuffer) self._loadClickBuffer();
        }).catch(() => {});
      }

      // NO pre-reproducir SFX aquí: consumiría el gesto de usuario
      // y puede impedir que bgMusic arranque (el navegador solo
      // permite un número limitado de play() por gesto). Los SFX
      // funcionan correctamente cuando se llaman desde playSFX()
      // porque en ese punto el usuario ya ha interactuado.

      // Eliminar todos los listeners tras el primer gesto
      eventos.forEach((ev) => document.removeEventListener(ev, onFirstGesture, true));
    }

    eventos.forEach((ev) => document.addEventListener(ev, onFirstGesture, { once: false, capture: true }));
  }

  /**
   * Inicializar efectos de sonido
   */
  initializeSFX() {
    Object.keys(this.sfxFiles).forEach((key) => {
      this.sfx[key] = new Audio(this._url(this.sfxFiles[key]));
      this.sfx[key].crossOrigin = 'anonymous';
      this.sfx[key].volume = this.targetVolume * this.sfxRatio;
    });
  }

  /**
   * Actualizar volumen de todos los SFX proporcionalmente al master
   */
  _syncSFXVolume() {
    const sfxVol = this.targetVolume * this.sfxRatio;
    Object.keys(this.sfx).forEach((key) => {
      this.sfx[key].volume = sfxVol;
    });
  }

  /**
   * Setup listeners para eventos globales
   */
  setupEventListeners() {
    // Pausa audio cuando se cambia de pestaña y reanuda suavemente al volver,
    // siempre que el usuario no haya dejado el volumen en 0 (mute manual).
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        this.pauseMusic();
      } else if (document.visibilityState === 'visible') {
        // Equivalente a:
        // if (audio && audio.paused && !silenciado) { audio.play().catch(() => {}); }
        const isMuted = (this.currentVolume <= 0.001) || (this.targetVolume <= 0.001);
        if (this.bgMusic && this.bgMusic.paused && !isMuted && this.currentTrach) {
          this.bgMusic
            .play()
            .then(() => {
              this.isPlaying = true;
              this._updateUI();
            })
            .catch(() => {
              // Ignorar silenciosamente errores de autoplay o políticas del navegador
            });
        }
      }
    });
  }

  /**
   * Reproducir pista musical con fade in
   * @param {string} trackKey - Clave de la pista (ej: 'prueba1')
   * @param {number} fadeInDuration - Duración del fade in en ms (default 2000)
   */
  playMusic(trackKey, fadeInDuration = 2000) {
    const track = this.tracks[trackKey];

    if (!track) {
      console.warn(`Track '${trackKey}' no encontrado`);
      return;
    }

    // Si es la misma canción y está sonando, no hacer nada
    if (this.currentTrach === trackKey && this.isPlaying) {
      return;
    }

    // Si hay música sonando, hacer fade out primero
    if (this.isPlaying) {
      this.fadeOut(1000).then(() => {
        this._loadAndPlayTrack(trackKey, fadeInDuration);
      });
    } else {
      this._loadAndPlayTrack(trackKey, fadeInDuration);
    }
  }

  /**
   * Método interno para cargar y reproducir la pista
   */
  _loadAndPlayTrack(trackKey, fadeInDuration) {
    const track = this.tracks[trackKey];
    const url = this._url(track.file);

    console.log(`🎵 Cargando audio: ${url}`);

    this.bgMusic.src = url;
    this.bgMusic.volume = 0;
    this.currentVolume = 0;
    this.currentTrach = trackKey;

    this.bgMusic
      .play()
      .then(() => {
        this.isPlaying = true;
        this._fadeInMusic(fadeInDuration);
        this._updateUI();
      })
      .catch((err) => {
        console.error('Error reproduciendo música:', err);
        // El navegador bloqueó el autoplay; se activará en el primer clic
        this._updateUI();
      });
  }

  /**
   * Fade in interno
   */
  _fadeInMusic(duration) {
    this.isFading = true;
    const startTime = Date.now();
    const step = 50;
    const target = this.targetVolume;

    const fadeInterval = setInterval(() => {
      const elapsed = Date.now() - startTime;
      const progress = Math.min(elapsed / duration, 1);

      this.currentVolume = target * progress;
      this.bgMusic.volume = this.currentVolume;

      if (progress >= 1) {
        clearInterval(fadeInterval);
        this.isFading = false;
        this.currentVolume = target;
        this.bgMusic.volume = target;
        this._updateUI();
      }
    }, step);
  }

  /**
   * Hacer fade out de la música actual
   * @param {number} duration - Duración en ms
   * @returns {Promise}
   */
  fadeOut(duration = 2000) {
    return new Promise((resolve) => {
      if (!this.isPlaying) {
        resolve();
        return;
      }

      this.isFading = true;
      const startTime = Date.now();
      const startVolume = this.currentVolume;

      const fadeInterval = setInterval(() => {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);

        this.currentVolume = startVolume * (1 - progress);
        this.bgMusic.volume = this.currentVolume;

        if (progress >= 1) {
          clearInterval(fadeInterval);
          this.bgMusic.pause();
          this.bgMusic.currentTime = 0;
          this.isPlaying = false;
          this.isFading = false;
          this._updateUI();
          resolve();
        }
      }, 50);
    });
  }

  /**
   * Pausar música
   */
  pauseMusic() {
    if (this.isPlaying) {
      this.bgMusic.pause();
      this.isPlaying = false;
      this._updateUI();
    }
  }

  /**
   * Reanudar música
   */
  resumeMusic() {
    if (!this.isPlaying && this.currentTrach) {
      this.bgMusic
        .play()
        .then(() => {
          this.isPlaying = true;
          this._updateUI();
        })
        .catch((err) => {
          console.error('Error reanudando música:', err);
        });
    }
  }

  /**
   * Toggle pausa/reproducción
   */
  togglePlayPause() {
    if (this.isPlaying) {
      this.pauseMusic();
    } else {
      this.resumeMusic();
    }
  }

  /**
   * Establecer volumen de música (0-1)
   * @param {number} value
   */
  setVolume(value) {
    // En móvil, el SO controla el volumen; ignorar cambios por JS
    if (this.isMobile) return;

    const vol = Math.max(0, Math.min(1, value));
    this.targetVolume = vol;
    this.currentVolume = vol;
    // Solo actualizar bgMusic.volume si no estamos en fade ni en ducking
    if (!this.isFading && !this._isDucking) {
      this.bgMusic.volume = vol;
    }
    this._syncSFXVolume();
    localStorage.setItem('audioVolume', vol.toFixed(3));
    this._updateUI();
  }

  /**
   * Audio Ducking: reduce temporalmente el volumen de la música de fondo
   * para que otros sonidos (como las pistas habladas) destaquen.
   * @param {number} duckLevel - Nivel al que reducir (0-1), ej: 0.1 = 10%
   * @param {number} fadeDuration - Duración del fade en ms
   */
  duckMusic(duckLevel = 0.08, fadeDuration = 300) {
    if (this._isDucking) return; // Ya está en ducking

    this._isDucking = true;
    this._volumeBeforeDuck = this.bgMusic.volume;

    const targetDuckVol = Math.max(duckLevel, 0.02);
    const startVol = this.bgMusic.volume;
    const startTime = Date.now();

    const fadeInterval = setInterval(() => {
      const elapsed = Date.now() - startTime;
      const progress = Math.min(elapsed / fadeDuration, 1);

      this.bgMusic.volume = startVol - (startVol - targetDuckVol) * progress;

      if (progress >= 1) {
        clearInterval(fadeInterval);
        this.bgMusic.volume = targetDuckVol;
      }
    }, 20);
  }

  /**
   * Restaurar el volumen después del ducking
   * @param {number} fadeDuration - Duración del fade de restauración en ms
   */
  unduckMusic(fadeDuration = 400) {
    if (!this._isDucking) return;

    const targetVol = this._volumeBeforeDuck || this.targetVolume;
    const startVol = this.bgMusic.volume;
    const startTime = Date.now();

    const fadeInterval = setInterval(() => {
      const elapsed = Date.now() - startTime;
      const progress = Math.min(elapsed / fadeDuration, 1);

      this.bgMusic.volume = startVol + (targetVol - startVol) * progress;

      if (progress >= 1) {
        clearInterval(fadeInterval);
        this.bgMusic.volume = targetVol;
        this._isDucking = false;
        this._volumeBeforeDuck = null;
      }
    }, 20);
  }

  /**
   * Aumentar volumen
   * @param {number} step
   */
  increaseVolume(step = 0.1) {
    this.setVolume(this.targetVolume + step);
  }

  /**
   * Disminuir volumen
   * @param {number} step
   */
  decreaseVolume(step = 0.1) {
    this.setVolume(this.targetVolume - step);
  }

  /**
   * Reproducir efecto de sonido.
   * Clona el elemento de audio para que pulsaciones rápidas no se corten
   * entre sí; el clon se elimina automáticamente al terminar.
   * @param {string} sfxKey
   */
  playSFX(sfxKey) {
    // Click: usar Web Audio API para latencia mínima
    if (sfxKey === 'click') {
      if (this._playClickWebAudio()) return;
    }

    if (!this.sfx[sfxKey]) {
      console.warn(`SFX '${sfxKey}' no encontrado`);
      return;
    }

    try {
      // Clonar el nodo de audio para permitir reproducción simultánea
      const clone = this.sfx[sfxKey].cloneNode();
      clone.volume = this.targetVolume * this.sfxRatio;
      clone.play().catch(() => {});
      // Liberar el clon cuando termine para no acumular nodos en memoria
      clone.addEventListener('ended', () => { clone.remove(); }, { once: true });
    } catch (err) {
      // Fallback: reproducir el original reiniciándolo
      try {
        this.sfx[sfxKey].currentTime = 0;
        this.sfx[sfxKey].play().catch(() => {});
      } catch(e) {}
    }
  }

  /**
   * Obtener información de la pista actual
   * @returns {object|null}
   */
  getCurrentTrackInfo() {
    if (!this.currentTrach) return null;
    return this.tracks[this.currentTrach];
  }

  /**
   * Actualizar UI del reproductor
   */
  _updateUI() {
    const event = new CustomEvent('audioStateChanged', {
      detail: {
        isPlaying: this.isPlaying,
        track: this.currentTrach,
        volume: this.currentVolume,
        trackInfo: this.getCurrentTrackInfo(),
      },
    });
    document.dispatchEvent(event);
  }

  /**
   * Restaurar configuración guardada
   */
  restoreSettings() {
    const savedVolume = localStorage.getItem('audioVolume');
    if (savedVolume !== null) {
      const vol = parseFloat(savedVolume);
      if (!isNaN(vol) && vol >= 0 && vol <= 1) {
        this.setVolume(vol);
      }
    }
  }
}

// Instancia global
const audioManager = new AudioManager();
audioManager.restoreSettings();

// Hacer disponible globalmente en window
window.audioManager = audioManager;

// Notificar a los componentes Blade que ya pueden usar audioManager
document.dispatchEvent(new Event('audioManagerReady'));
