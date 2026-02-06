<?php

namespace App\Http\Requests\Auth;

use App\Traits\ApiValidationTrait;
use Illuminate\Foundation\Http\FormRequest;


class LoginRequest extends FormRequest
{
    use ApiValidationTrait;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Email is required',
            'email.email'       => 'Email must be valid',
            'password.required' => 'Password is required',
        ];
    }
}
