<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        // Allow all users for now, but you can restrict it based on roles if needed
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'application_role_id' => 'required|exists:application_roles,id',
            'description' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
