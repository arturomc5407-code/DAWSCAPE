<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    protected $table = 'valoraciones';
    
    protected $fillable = [
        'partida_id',
        'nombre_equipo',
        'estrellas',
        'comentario',
    ];

    public function partida()
    {
        return $this->belongsTo(Partida::class);
    }
}
