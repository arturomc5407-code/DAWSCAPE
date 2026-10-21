<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarPrueba5Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Normaliza los datos antes de la validación
     * Elimina espacios en blanco del checksum
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'checksum' => trim($this->checksum ?? ''),
        ]);
    }

    public function rules()
    {
        return [
            'checksum' => 'required|string'
        ];
    }
}
