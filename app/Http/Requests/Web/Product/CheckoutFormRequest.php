<?php

namespace App\Http\Requests\Web\Product;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required',
            'quantity'=>'required',
            'size' => 'required',
            'user_id' => 'required',
        ];
    }
}
