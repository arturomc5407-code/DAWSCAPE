<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TiempoAjustado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $uuid;
    public int $segundos;
    public string $tipo;
    public int $penalizacion_total;

    public function __construct(string $uuid, int $segundos, string $tipo, int $penalizacionTotal)
    {
        $this->uuid = $uuid;
        $this->segundos = $segundos;
        $this->tipo = $tipo;
        $this->penalizacion_total = $penalizacionTotal;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('equipo.' . $this->uuid),
        ];
    }
}
