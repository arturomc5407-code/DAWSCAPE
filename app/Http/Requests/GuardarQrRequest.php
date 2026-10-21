<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarQrRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Asumimos que el middleware AdminAuth ya protege la ruta general
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fase_requerida' => 'nullable|string|max:255'
        ];
    }
}
