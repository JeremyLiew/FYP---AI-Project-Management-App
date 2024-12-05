<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method
    }

    public function rules(): array
    {
        return [
            'id' => 'required',
            'name' => 'required|string|max:255',  // The budget name is required and should be a string
            'total_budget' => 'required|numeric|min:0',  // The total budget should be a numeric value and cannot be negative
        ];
    }
}
