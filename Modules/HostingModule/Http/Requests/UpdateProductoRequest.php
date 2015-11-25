<?php
/**
 * Created by PhpStorm.
 * User: Carlos
 * Date: 22/11/2015
 * Time: 16:23
 */

namespace Modules\HostingModule\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'disklimit' => 'required',
            'bwlimit' => 'required',
            'emailaccounts' => 'required',
            'ftpaccounts' => 'required',
            'paquete' => 'required',
            'description' => 'required',
            'server_id' => 'required',
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