<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class GetBudgetListingsRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method
    }

    public function rules()
    {
        return [
            'itemsPerPage' => 'nullable|integer|min:1', // Number of items per page
            'searchQuery' => 'nullable|string|max:255', // Search by budget name or other attributes
        ];
    }
}
