<?php

namespace App\Services\Payments;

class StripeGateway implements PaymentGatewayInterface
{
    protected string $clientId;
    protected string $secret;
    public function __construct(protected array $config)
    {
        $this->clientId = $config['client_id']
            ?? config('services.stripe.client_id');

        $this->secret = $config['client_secret']
            ?? config('services.stripe.client_secret');
    }

    public function pay(array $data): array
    {
        return [
            'status' => 'success',
            'amount' => $data['amount'],
            'order_id' => $data['order_id'],
            'payment_method' => 'stripe',
            'transaction_id' => uniqid('stripe_'),
        ];
    }
}
