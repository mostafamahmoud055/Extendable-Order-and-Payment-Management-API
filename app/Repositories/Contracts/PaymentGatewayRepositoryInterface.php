<?php
namespace App\Repositories\Contracts;

use App\Models\PaymentGateway;

interface PaymentGatewayRepositoryInterface
{
    public function findByName(string $name): ?PaymentGateway;
    public function create(array $data): PaymentGateway;
    public function update(PaymentGateway $gateway, array $data): PaymentGateway;
}
