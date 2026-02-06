<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PaymentGateway;
use App\Enums\PaymentGatewayEnum;

class PaymentGatewayFactory extends Factory
{
    protected $model = PaymentGateway::class;

    public function definition(): array
    {
        return [
            'name' => PaymentGatewayEnum::CREDIT_CARD,
            'config' => [
                'client_id' => $this->faker->uuid,
                'client_secret' => $this->faker->sha256,
            ],
        ];
    }
}
