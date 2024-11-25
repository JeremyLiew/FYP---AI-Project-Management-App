<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class GetTaskListingsRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method
    }

    public function rules()
    {
        return [
            'searchQuery' => 'nullable|string|max:255',
            'selectedFilter' => 'nullable|in:All,Ongoing,Completed,Pending',
            'selectedPriority' => 'nullable|in:All,Low,Medium,High',
        ];
    }
}
