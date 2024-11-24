<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class GetProjectListingsRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to access this method
    }

    public function rules()
    {
        return [
            'itemsPerPage' => 'nullable|integer|min:1',
            'searchQuery' => 'nullable|string|max:255',
            'selectedFilter' => 'nullable|in:All,Ongoing,Completed,Pending',
        ];
    }
}
