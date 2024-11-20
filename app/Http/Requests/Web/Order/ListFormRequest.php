<?php

namespace App\Http\Requests\Web\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListFormRequest extends FormRequest
{
    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'id' => 'required',
            "page" => ["nullable", "integer"],
            "itemsPerPage" => ["nullable", "integer"],
        ];
    }

    /**
    * Custom message for validation
    *
    * @return array
    */
    public function messages()
    {
        return [
            // 'firstName.required' => 'Page\'s Title field is required.',
        ];

    }
}
