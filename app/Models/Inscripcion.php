<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripcions';

    protected $fillable = [
        'nombre_equipo',
        'correo',
        'clase',
        'qr_token',
        'codigo_acceso',
        'aceptado_manualmente',
    ];

    /**
     * Generar un token QR único y un código de acceso antes de crear la inscripción.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generar QR token único (32 caracteres)
            if (empty($model->qr_token)) {
                $model->qr_token = Str::random(32);
            }

            // Generar código de acceso único (6 caracteres alfanuméricos en mayúsculas)
            if (empty($model->codigo_acceso)) {
                do {
                    $codigo = strtoupper(Str::random(6));
                } while (self::where('codigo_acceso', $codigo)->exists());

                $model->codigo_acceso = $codigo;
            }
        });
    }
}
