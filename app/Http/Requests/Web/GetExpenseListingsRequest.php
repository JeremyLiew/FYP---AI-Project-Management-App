<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class GetExpenseListingsRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method
    }

    public function rules(): array
    {
        return [
            'itemsPerPage' => 'nullable|integer|min:1|max:100', // Pagination limit
            'searchQuery' => 'nullable|string|max:255', // Search term
        ];
    }

    public function messages()
    {
        return [
            'itemsPerPage.integer' => 'The items per page must be a valid number.',
            'itemsPerPage.min' => 'The items per page must be at least 1.',
            'itemsPerPage.max' => 'The items per page must not exceed 100.',
            'searchQuery.max' => 'The search query must not exceed 255 characters.',
        ];
    }
}
