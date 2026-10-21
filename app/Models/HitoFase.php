<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HitoFase extends Model
{
    protected $table = 'hitos_fases';

    protected $fillable = [
        'partida_id',
        'fase',
        'segundos_transcurridos',
        'alcanzado_at'
    ];

    protected $casts = [
        'alcanzado_at' => 'datetime',
    ];

    public function partida()
    {
        return $this->belongsTo(Partida::class);
    }
}
