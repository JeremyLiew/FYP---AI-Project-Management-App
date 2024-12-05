<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class CreateBudgetRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255', // Budget name is required
            'total_budget' => 'required|numeric|min:0', // Total budget must be a positive number
            'remaining_amount' => 'nullable|numeric|min:0', // Remaining amount, optional but must be a positive number
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The budget name is required.',
            'total_budget.required' => 'The total budget is required.',
            'total_budget.numeric' => 'The total budget must be a valid number.',
            'remaining_amount.numeric' => 'The remaining amount must be a valid number.',
        ];
    }
}
