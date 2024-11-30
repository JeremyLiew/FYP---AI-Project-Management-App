<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Validation\Rule;

class LoginFormRequest extends FormRequest
{
    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'email' => ["required", "email"],
            'password' => ['required'],
            'device_name' => ['nullable', 'string'],
            'remember' => ['nullable', 'boolean'],
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
