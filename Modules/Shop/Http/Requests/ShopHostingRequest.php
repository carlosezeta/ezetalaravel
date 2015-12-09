<?php

namespace Modules\Shop\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ShopHostingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'domain' => 'required',
            'domainoption' => 'required'
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