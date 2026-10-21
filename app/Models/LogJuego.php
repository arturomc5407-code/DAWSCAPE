<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogJuego extends Model
{
    protected $fillable = [
        'partida_id',
        'fase',
        'accion',
        'detalles'
    ];

    protected $casts = [
        'detalles' => 'array',
    ];

    public function partida()
    {
        return $this->belongsTo(Partida::class);
    }
}
