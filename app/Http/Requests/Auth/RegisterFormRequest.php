<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterFormRequest extends FormRequest
{
    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'name' => ['required', Rule::unique('users', 'name')],
            'email' => ["required", "email" , Rule::unique('users', 'email')],
            'password' => [
            'required',
            'string',
            'min:6',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/',
            'confirmed'
        ],
        'password_confirmation' => ['required', 'string', 'same:password'],
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
            'password.regex' => 'Password must have at least 6 characters, include an uppercase letter, a lowercase letter, a number, and a special character.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];

    }

}
