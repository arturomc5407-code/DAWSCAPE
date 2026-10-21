<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PistaUsada extends Model
{
    protected $table = 'pistas_usadas';

    protected $fillable = [
        'partida_id',
        'prueba_numero',
    ];

    public function partida()
    {
        return $this->belongsTo(Partida::class);
    }
}
