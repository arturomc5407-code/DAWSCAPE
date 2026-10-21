<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Inscripcion;

class EmpezarPartidaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'correo' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (!str_ends_with($value, '@estudiante.xtart.com')) {
                        $fail('ACCESO DENEGADO: Usar correo corporativo (@estudiante.xtart.com).');
                    }

                    // Validar que el correo esté inscrito
                    $inscripcion = Inscripcion::where('correo', $value)->first();
                    if (!$inscripcion) {
                        return $fail('¡Tu correo no está registrado! Dirígete a /inscripción primero.');
                    }
                },
            ],
            'icono' => [
                'nullable',
                'string',
            ],
            'color_hex' => [
                'nullable',
                'string',
            ],
            'codigo_acceso' => [
                'required',
                'string',
                'size:6',
                function ($attribute, $value, $fail) {
                    // Validar que el código sea válido
                    $inscripcion = Inscripcion::where('codigo_acceso', strtoupper($value))->first();
                    if (!$inscripcion) {
                        $fail('Código de acceso inválido.');
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'correo.required' => 'El correo es obligatorio.',
            'codigo_acceso.required' => 'El código de acceso es obligatorio.',
            'codigo_acceso.size' => 'El código debe tener 6 caracteres.',
        ];
    }
}

