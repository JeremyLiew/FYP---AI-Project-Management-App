<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust as necessary for authorization logic
    }

    public function rules()
    {
        return [
            'comment_id' => 'required|exists:comments,id',
            'comment' => 'required|string|max:1000',
        ];
    }
}
