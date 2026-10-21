<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarPrueba1Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'completed' => 'required|boolean',
            'answers'   => 'required|array|min:6'
        ];
    }
}
