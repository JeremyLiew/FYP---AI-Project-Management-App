<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParamIdFormRequest extends FormRequest
{
	use FormRequestTrait;

    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id')
        ]);
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'id' => 'required|integer',
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
