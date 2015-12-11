<?php

namespace Modules\Shop\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnviarPagoRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'required|string|min:2|max:32',
            'last_name' => 'required|string|min:2|max:32',
            'email' => 'required|email'
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }
}