<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Enums\PaymentGatewayEnum;
use Illuminate\Support\Facades\Cache;
use App\Services\Payments\PayPalGateway;
use App\Services\Payments\StripeGateway;
use App\Services\Payments\CreditCardGateway;
use App\Services\Payments\PaymentGatewayInterface;
use App\Repositories\Contracts\PaymentGatewayRepositoryInterface;

class PaymentGatewayService
{
    public function __construct(protected PaymentGatewayRepositoryInterface $repository) {}


    public function getGateway(string $gateway): PaymentGatewayInterface | array
    {
        $gatewayEnum = PaymentGatewayEnum::from($gateway);

        $gatewayModel = Cache::remember(
            "payment_gateway_{$gatewayEnum->value}",
            now()->addMinutes(30),
            fn() => $this->repository->findByName($gatewayEnum->value)
        );

        if (!$gatewayModel) {
            return ["error" => "Gateway not found", "status" => 404];
        }

        return match ($gatewayEnum) {
            PaymentGatewayEnum::PAYPAL => new PayPalGateway($gatewayModel->config),
            PaymentGatewayEnum::CREDIT_CARD => new CreditCardGateway($gatewayModel->config),
            PaymentGatewayEnum::STRIPE => new StripeGateway($gatewayModel->config),
        };
    }


    public function createGateway(array $data): PaymentGateway
    {
        return $this->repository->create($data);
    }


    public function updateGateway(PaymentGatewayEnum $gatewayEnum, array $data): PaymentGateway | array
    {
        $gateway = $this->repository->findByName($gatewayEnum->value);
        if (!$gateway) return ["error" => "Gateway not found", "status" => 404];

        $updated = $this->repository->update($gateway, $data);

        Cache::forget("payment_gateway_{$gatewayEnum->value}");

        return $updated;
    }
}
