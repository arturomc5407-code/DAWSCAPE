@php
    $faseNombre = collect(config('fases.fases'))->firstWhere('db', $equipo->fase_actual)['nombre'] ?? $equipo->fase_actual;
    $icon = $equipo->icono ?? '🎮';
    $color = $equipo->color_hex ?? '#3b82f6';

    $inicio = \Carbon\Carbon::parse($equipo->inicio_juego);
    $ahora = \Carbon\Carbon::now();
    $tiempoTotal = (int) abs($ahora->diffInSeconds($inicio));
    $segundosDesdePausa = 0;
    if ($equipo->estado === 'pausado' && $equipo->ultima_pausa_at) {
        $segundosDesdePausa = (int) abs($ahora->diffInSeconds(\Carbon\Carbon::parse($equipo->ultima_pausa_at)));
    }
    $elapsed = max(0, $tiempoTotal - ($equipo->tiempo_pausado_segundos ?? 0) - $segundosDesdePausa) + ($equipo->penalizacion_segundos ?? 0);
    $mins = intdiv($elapsed, 60);
    $secs = $elapsed % 60;
    $penalty = $equipo->penalizacion_segundos ?? 0;
@endphp

<div class="team-card" id="team-{{ $equipo->uuid }}" style="border-left: 3px solid {{ $color }}">
    <div class="team-header">
        <div class="team-info">
            <span class="team-icon">
                @if(str_starts_with($icon, 'ph-'))
                    <i class="ph {{ $icon }}"></i>
                @else
                    {{ $icon }}
                @endif
            </span>
            <span class="team-name">{{ $equipo->nombre_equipo }}</span>
        </div>
        <span class="team-class">{{ $equipo->clase ?? '—' }}</span>
    </div>
    <div class="team-phase">► {{ $faseNombre }}</div>
    <div class="team-time">⏱ <span class="elapsed">{{ $mins }}m {{ str_pad($secs, 2, '0', STR_PAD_LEFT) }}s</span>
        @if($penalty > 0)
            |  ⚠ <span class="penalty">+{{ intdiv($penalty,60) }}m {{ str_pad($penalty%60,2,'0',STR_PAD_LEFT) }}s pen.</span>
        @endif
    </div>
    <div class="team-actions">
        <button class="btn-penalty" onclick="ajustarTiempo('{{ $equipo->uuid }}', 300, this)" title="Añadir 5 min de penalización">+5m PEN</button>
        <button class="btn-bonus" onclick="ajustarTiempo('{{ $equipo->uuid }}', -300, this)" title="Restar 5 min (bonus/pista)">−5m BONUS</button>
    </div>
</div>
