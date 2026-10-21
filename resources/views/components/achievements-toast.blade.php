@if(session()->has('nuevos_logros') && count(session('nuevos_logros')) > 0)
<style>
.achievement-toast {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%) translateY(-100px);
    background: rgba(4, 26, 14, 0.95);
    border: 2px solid #eab308;
    padding: 1rem 2rem;
    border-radius: 8px;
    box-shadow: 0 0 30px rgba(234, 179, 8, 0.4);
    display: flex;
    align-items: center;
    gap: 15px;
    z-index: 99999;
    font-family: 'Share Tech Mono', monospace;
    opacity: 0;
    animation: slideDownFadeOut 6s forwards;
}

.ach-icon {
    font-size: 2.5rem;
}

.ach-content h4 {
    color: #eab308;
    margin: 0;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.ach-content p {
    color: #fff;
    margin: 5px 0 0 0;
    font-size: 1.2rem;
    font-weight: bold;
}

@keyframes slideDownFadeOut {
    0% { transform: translateX(-50%) translateY(-100px); opacity: 0; }
    10% { transform: translateX(-50%) translateY(0); opacity: 1; }
    80% { transform: translateX(-50%) translateY(0); opacity: 1; }
    100% { transform: translateX(-50%) translateY(-100px); opacity: 0; }
}
</style>

@php
    $logrosList = session('nuevos_logros');
    session()->forget('nuevos_logros');

    $dict = [
        'speedrunner_ninja' => ['icon' => 'ph-lightning', 'name' => 'Speedrunner Ninja', 'color' => '#eab308'],
        'cerebro_intacto' => ['icon' => 'ph-brain', 'name' => 'Cerebro Intacto', 'color' => '#ec4899'],
        'hackerman' => ['icon' => 'ph-terminal-window', 'name' => 'Hackerman', 'color' => '#10b981'],
    ];
@endphp

@foreach($logrosList as $l)
    @php $data = $dict[$l] ?? ['icon' => 'ph-medal', 'name' => 'Logro Secreto', 'color' => '#fff']; @endphp
    <div class="achievement-toast">
        <div class="ach-icon" @style(['color' => $data['color'], 'filter' => 'drop-shadow(0 0 10px '.$data['color'].')'])>
            <i class="ph-fill {{ $data['icon'] }}"></i>
        </div>
        <div class="ach-content">
            <h4>LOGRO DESBLOQUEADO</h4>
            <p @style(['color' => $data['color']])>{{ $data['name'] }}</p>
        </div>
    </div>
@endforeach

<script>
    // Reproducir sonido de logro (opcional si hay sfx disponible)
</script>
@endif
