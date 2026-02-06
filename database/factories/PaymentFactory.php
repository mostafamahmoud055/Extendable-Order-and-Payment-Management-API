<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'payment_method' => 'CREDIT_CARD',
            'status' => 'successful',
            'reference_id' => Str::uuid()->toString(),
            'total_amount' => $this->faker->randomFloat(2, 50, 500),
        ];
    }
}
