<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255', // Expense name is required
            'expense_category_id' => 'required|exists:expense_categories,id', // Must reference an existing expense category
            'project_id' => 'nullable|exists:projects,id', // Optional, but must reference an existing project
            'task_id' => 'nullable|exists:tasks,id', // Optional, but must reference an existing task
            'amount' => 'required|numeric|min:1', // Amount is required and must be positive
            'description' => 'nullable|string', // Optional description
            'date_incurred' => 'required|date',

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The expense name is required.',
            'expense_category_id.required' => 'The expense category is required.',
            'expense_category_id.exists' => 'The selected expense category does not exist.',
            'project_id.exists' => 'The selected project does not exist.',
            'task_id.exists' => 'The selected task does not exist.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a valid number.',
            'date_incurred.date' => 'The date incurred must be a valid date.',
        ];
    }
}
