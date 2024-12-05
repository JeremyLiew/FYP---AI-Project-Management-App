<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpenseCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:expense_categories,name', // Name is required and must be unique
            'description' => 'nullable|string|max:1000', // Optional description with a maximum length
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The category name is required.',
            'name.unique' => 'This category name already exists.',
            'name.max' => 'The category name must not exceed 255 characters.',
            'description.max' => 'The description must not exceed 1000 characters.',
        ];
    }
}
