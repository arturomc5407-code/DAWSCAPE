<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaDinamica extends Model
{
    protected $table = 'pregunta_dinamicas';
    protected $fillable = ['enunciado', 'respuesta_correcta', 'respuestas_alternativas', 'activa'];

    protected $casts = [
        'respuestas_alternativas' => 'array',
        'activa' => 'boolean',
    ];
}
