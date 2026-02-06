<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ApiValidationTrait
{
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();

        $message = implode(', ', array_map(fn($v) => $v[0], $errors));

        throw new HttpResponseException(response()->json([
            'status' => 'error',      
            'message' => 'Validation Errors',
            'data' => [],
            'errors' => $errors
        ], 422));
    }
}
