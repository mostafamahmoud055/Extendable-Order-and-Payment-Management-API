<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentGatewayEnum;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentService
{
    public function __construct(protected PaymentRepositoryInterface $paymentRepo, protected PaymentGatewayService $gatewayService) {}

    public function processPayment(int $id, string $method)
    {
        $order = Order::find($id);
        if (!$order) {
            return ["error" => "Order not found", "status" => 404];
        }

        if (!$order->canProcessPayment()) {
            return ["error" => "Order cannot be processed for payment", "status" => 400];
        }

        $gateway = $this->resolveGateway($method);

        if (is_array($gateway) && isset($gateway['error'])) {
            return $gateway;
        }

        $result = $gateway->pay(['amount' => $order->total_amount, 'order_id' => $order->id]);

        $paymentStatus = $this->determinePaymentStatus($result['status']);

        $payment = $this->createPaymentRecord($order, $method, $paymentStatus, $result);

        if ($paymentStatus === PaymentStatusEnum::SUCCESSFUL) {
            return [
                'status' => $payment,
            ];
        }

        return ["error" => "Payment cannot be processed for order ID: {$order->id}", "status" => 400];
    }

    public function getAllPayments(array $filters = [])
    {
        return $this->paymentRepo->all($filters);
    }


    public function getPaymentByReferenceId(string $reference_id)            
    {
        $payment = $this->paymentRepo->find($reference_id);

        if (!$payment) {
            return ["error" => "Payment not found", "status" => 404];
        }

        return $payment;
    }

    protected function determinePaymentStatus(string $status): PaymentStatusEnum
    {
        return strtolower($status) === 'success'
            ? PaymentStatusEnum::SUCCESSFUL
            : PaymentStatusEnum::FAILED;
    }

    protected function resolveGateway(string $method): mixed
    {
        $gatewayEnum = PaymentGatewayEnum::tryFrom($method);

        $gateway = $this->gatewayService->getGateway($gatewayEnum->value);

        if (is_array($gateway) && isset($gateway['error'])) {
            return $gateway;
        }

        return $gateway;
    }

    protected function createPaymentRecord(Order $order, string $gateway, PaymentStatusEnum $status, array $gatewayResult)
    {
        return $this->paymentRepo->create([
            'order_id' => $order->id,
            'payment_method' => $gateway,
            'status' => $status->value,
            'total_amount' => $order->total_amount,
            'reference_id' => $gatewayResult['transaction_id'],
        ]);
    }
}
