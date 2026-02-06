<?php
namespace App\Repositories\Eloquent;

use App\Models\PaymentGateway;
use App\Repositories\Contracts\PaymentGatewayRepositoryInterface;

class PaymentGatewayRepository implements PaymentGatewayRepositoryInterface
{
    public function findByName(string $name): PaymentGateway | null
    {
        return PaymentGateway::where('name', $name)->first();
    }

    public function create(array $data): PaymentGateway
    {
        return PaymentGateway::create($data);
    }

    public function update(PaymentGateway $gateway, array $data): PaymentGateway
    {
        $gateway->update($data);
        return $gateway;
    }
}
