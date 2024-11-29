<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class CreateCommentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust as necessary for authorization logic
    }

    public function rules()
    {
        return [
            'task_id' => 'required|exists:tasks,id',
            'comment' => 'required|string|max:1000',
            'creator' => 'required|exists:users,id',
        ];
    }
}
