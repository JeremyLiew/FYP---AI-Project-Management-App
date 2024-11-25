<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
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
            'due_date' => 'required|date|after:today',
            'status' => 'required|in:Ongoing,Completed,Pending',
            'priority' => 'required|in:Low,Medium,High,-',
            'project_id' => 'required|exists:projects,id',
        ];
    }
}
