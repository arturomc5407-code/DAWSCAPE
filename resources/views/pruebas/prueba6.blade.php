@extends('layouts.app')

@section('title', 'SOBRECARGA DEL SISTEMA | DAWSCAPE')

@push('styles')
    @vite(['resources/css/prueba6.css'])
@endpush

@section('content')

@include('components.game-audio-setup', ['trackKey' => 'prueba6'])

<!-- HEADER -->
<div class="game-header">
  <div class="team-info">
    <strong>Equipo:</strong> {{ session('nombre_equipo', 'Sin equipo') }}<br>
    <strong>Clase:</strong> {{ session('clase', 'N/A') }}
  </div>
  <div class="header-timer" id="cronometro">--:--</div>
</div>

<div class="screen" id="screen-game">
    <div class="title-glitch">RESTAURACIÓN DE ENERGÍA</div>

    <div class="lore-box">
        <strong>FALLO CASCADA DETECTADO.</strong><br>
        El sistema requiere la reintroducción manual de las secuencias de encendido.
        <br><br>Repartíos el esfuerzo mental: la frecuencia sobrecargará la memoria de un solo individuo.
    </div>

    <!-- Indicador de estado -->
    <div id="sys-status">SISTEMA A LA ESPERA</div>

    <button id="btn-start" class="btn-start">▶ INICIAR SECUENCIA</button>

    <div class="hud" style="display:none;" id="hud-container">
        <div>FASE: <span id="ronda-val" style="color:var(--green);font-weight:bold;">1/5</span></div>
        <div>PASOS: <span id="pasos-val" style="color:#00aaff;font-weight:bold;">0</span></div>
    </div>

    <!-- Tablero 2x2 -->
    <div class="simon-board" id="board">
        <div class="sector" id="btn-0"></div>
        <div class="sector" id="btn-1"></div>
        <div class="sector" id="btn-2"></div>
        <div class="sector" id="btn-3"></div>
    </div>

</div>

<!-- PANTALLA ÉXITO -->
<div class="screen" id="screen-success">
    <div style="font-size: 5rem; margin-bottom: 1rem; filter: drop-shadow(0 0 20px #00ff88);">⚡</div>
    <div class="title-glitch" style="margin-bottom:0.5rem">SISTEMA RESTAURADO</div>
    <div style="color:#88ccaa; font-size:1.1rem; margin-bottom: 2rem;">"Enrutamiento de energía cooperativo completado al 100%."</div>
    <a href="/prueba7" class="success-btn">CONTINUAR A LA SIGUIENTE PRUEBA →</a>
</div>

<!-- Elemento oculto que pasa los datos del servidor al JS -->
<div id="prueba6-config"
     data-tiempo="{{ $tiempo_transcurrido ?? 0 }}"
     data-sequence='@json(session("prueba6_sequence", []))'
     style="display:none;">
</div>

@push('scripts')
@vite(['resources/js/prueba6.js'])

<script>
  (function() {
    var ORIGINAL_VOLUME_KEY = 'prueba6_prevVolume';
    var REDUCED_VOLUME = 0.5;

    // Guarda el volumen actual y lo reduce al 50%
    window.prueba6ReduceVolume = function() {
      if (!window.audioManager) return;
      try {
        var current =
          (typeof window.audioManager.targetVolume === 'number'
            ? window.audioManager.targetVolume
            : (typeof window.audioManager.currentVolume === 'number'
              ? window.audioManager.currentVolume
              : 1));
        if (current > REDUCED_VOLUME + 0.02) {
          localStorage.setItem(ORIGINAL_VOLUME_KEY, current.toFixed(3));
        }
        window.audioManager.setVolume(REDUCED_VOLUME);
      } catch (e) {}
    };

    // Restaura el volumen original (100%)
    window.prueba6RestoreVolume = function() {
      if (!window.audioManager) return;
      try {
        var saved = parseFloat(localStorage.getItem(ORIGINAL_VOLUME_KEY));
        if (!isNaN(saved) && saved >= 0 && saved <= 1) {
          window.audioManager.setVolume(saved);
        } else {
          window.audioManager.setVolume(1);
        }
        localStorage.removeItem(ORIGINAL_VOLUME_KEY);
      } catch (e) {}
    };

    // Restaurar volumen si el usuario abandona la prueba 6
    window.addEventListener('beforeunload', function() {
      window.prueba6RestoreVolume();
    });
  })();
</script>
@endpush

@include('components.pista-ayuda', ['pruebaNumero' => 6, 'pistaConfig' => $pistaConfig, 'yaUsada' => $yaUsada])

@endsection

@include('components.live-listener')
