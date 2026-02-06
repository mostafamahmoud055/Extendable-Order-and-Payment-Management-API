<?php
namespace App\Services\Payments;

interface PaymentGatewayInterface
{
    public function pay(array $data): array;
}
