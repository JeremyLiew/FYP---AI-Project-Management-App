<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthPasswordUpdateFormRequest extends FormRequest
{
    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed']
        ];
    }

    /**
    * Custom message for validation
    *
    * @return array
    */
    public function messages()
    {
        return [
            // 'firstName.required' => 'Page\'s Title field is required.',
        ];

    }

}
