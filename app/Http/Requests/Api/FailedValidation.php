<?php


namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

trait FailedValidation
{
    public function failedValidation(Validator $validator)
    {
        $response = new Response([
            'error' => $validator->errors()->first()
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
