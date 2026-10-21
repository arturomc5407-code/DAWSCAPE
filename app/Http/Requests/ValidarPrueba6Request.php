<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarPrueba6Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'completed' => 'required|boolean',
            'expected_sequence' => 'required|array|min:15',
            'expected_sequence.*' => 'integer|min:0|max:3'
        ];
    }
}
