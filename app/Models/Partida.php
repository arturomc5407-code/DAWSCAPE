<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $fillable = [
        'email', 
        'nombre_equipo', 
        'clase', 
        'tiempo_segundos', 
        'inicio_juego', 
        'fase_actual',
        'uuid',
        'penalizacion_segundos',
        'color_hex',
        'icono',
        'logros',
        'estado',
        'ranking_posicion_bloqueada',
        'tiempo_pausado_segundos',
        'ultima_pausa_at',
        'combinacion_codice',
        'intentos_codice',
        'intentos_prueba9',
        'hints_used'
    ];

    protected $casts = [
        'logros' => 'array',
        'inicio_juego' => 'datetime',
        'ultima_pausa_at' => 'datetime',
        'combinacion_codice' => 'array'
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function hitosFases()
    {
        return $this->hasMany(HitoFase::class);
    }

    public function logs()
    {
        return $this->hasMany(LogJuego::class);
    }

    public function pistasUsadas()
    {
        return $this->hasMany(PistaUsada::class);
    }
}
