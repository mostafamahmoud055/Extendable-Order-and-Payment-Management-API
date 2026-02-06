<?php

namespace App\Http\Requests\PaymentGetway;

use App\Enums\PaymentGatewayEnum;
use App\Traits\ApiValidationTrait;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;


class StorePaymentGatewayRequest extends FormRequest
{
    use ApiValidationTrait;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', new Enum(PaymentGatewayEnum::class), 'unique:payment_gateways,name'],
            'config' => ['sometimes', 'array'],
        ];
        
        $gatewaysWithClientSecret = [
            PaymentGatewayEnum::PAYPAL->value,
            PaymentGatewayEnum::STRIPE->value,
            PaymentGatewayEnum::CREDIT_CARD->value,
        ];

        if (in_array($this->input('name'), $gatewaysWithClientSecret)) {
            $rules['config.client_id'] = ['required', 'string'];
            $rules['config.client_secret'] = ['required', 'string'];
        }

        return $rules;
    }
}
