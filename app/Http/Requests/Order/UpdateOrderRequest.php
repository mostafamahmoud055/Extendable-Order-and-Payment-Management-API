<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatusEnum;
use App\Traits\ApiValidationTrait;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    use ApiValidationTrait;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', new Enum(OrderStatusEnum::class)],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.product_name' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.price' => ['required_with:items', 'numeric', 'min:0'],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'user_id' => $this->user()->id,
        ]);
    }
}
