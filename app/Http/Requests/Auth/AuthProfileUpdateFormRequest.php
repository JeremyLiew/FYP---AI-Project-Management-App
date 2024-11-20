<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthProfileUpdateFormRequest extends FormRequest
{
    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'name' => ["required", "string", "max:50", "unique:users,name," . auth()->id()],
            // postcode, ...
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
