<?php

namespace App\Http\Controllers;

use App\Services\PaymentGatewayService;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\PaymentGetway\StorePaymentGatewayRequest;
use App\Http\Requests\PaymentGetway\UpdatePaymentGatewayRequest;
use App\Http\Resources\PaymentGatewayResource;
use App\Enums\PaymentGatewayEnum;

class PaymentGatewayController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected PaymentGatewayService $gatewayService) {}

    public function store(StorePaymentGatewayRequest $request)
    {
        $gateway = $this->gatewayService->createGateway($request->validated());
        return $this->successResponse(new PaymentGatewayResource($gateway), 'Payment Gateway created successfully', 201);
    }

    public function update(UpdatePaymentGatewayRequest $request, $name)
    {
        $gateway = $this->gatewayService->updateGateway(PaymentGatewayEnum::from($name), $request->validated());
        if (is_array($gateway) && isset($gateway['error'])) {
            return $this->errorResponse('Payment Gateway update failed', $gateway['status'], ['payment_gateway' => $gateway['error']]);
        }
        return $this->successResponse(new PaymentGatewayResource($gateway), 'Payment Gateway updated successfully');
    }
}
