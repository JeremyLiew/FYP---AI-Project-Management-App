<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:Ongoing,Completed,Pending',
            'priority' => 'required|in:Low,Medium,High,-',
            'budget_id' => 'required|exists:budgets,id',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:project_roles,id',
            'roles' => 'size:' . count($this->input('members')),
             'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ];
    }

    public function messages()
    {
        return [
            'roles.size' => 'The number of members and roles must match.',
        ];
    }
}
