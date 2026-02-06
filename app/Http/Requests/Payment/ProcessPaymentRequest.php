<?php

namespace App\Http\Requests\Payment;

use App\Enums\PaymentGatewayEnum;
use App\Traits\ApiValidationTrait;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    use ApiValidationTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', new Enum(PaymentGatewayEnum::class)],
        ];
    }
}
