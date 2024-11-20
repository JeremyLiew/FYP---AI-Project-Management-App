<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait FormRequestTrait
{
	protected function failedValidation(Validator $validator)
	{
		$errors = $this->validator->errors();

		throw new HttpResponseException(
			response()->json([
				'message' => $errors->first(),
				'errors' => $errors,
			], 422)
		);
	}
}
