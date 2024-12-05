<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method (you can modify this if needed)
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255', // Expense category name is required
            'description' => 'nullable|string|max:1000', // Optional description for the category
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The expense category name is required.',
            'name.string' => 'The expense category name must be a string.',
            'name.max' => 'The expense category name cannot exceed 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description cannot exceed 1000 characters.',
        ];
    }
}
