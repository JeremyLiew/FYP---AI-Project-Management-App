<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255', // Expense name is required
            'expense_category_id' => 'required|exists:expense_categories,id', // Must reference a valid expense category
            'project_id' => 'nullable|exists:projects,id', // Optional, must reference a valid project
            'task_id' => 'nullable|exists:tasks,id', // Optional, must reference a valid task
            'amount' => 'required|numeric|min:0', // Expense amount is required and must be positive
            'description' => 'nullable|string|max:1000', // Optional description
            'date_incurred' => 'required|date', // Expense date is required
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
            'amount.required' => 'The expense amount is required.',
            'amount.numeric' => 'The expense amount must be a valid number.',
            'amount.min' => 'The expense amount must be a positive number.',
            'description.max' => 'The description must not exceed 1000 characters.',
            'date_incurred.date' => 'The expense date must be a valid date.',
        ];
    }
}
