<?php

namespace App\Http\Controllers;


use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\PaymentResource;
use App\Http\Requests\Payment\ProcessPaymentRequest;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected PaymentService $paymentService) {}

    public function processPayment(ProcessPaymentRequest $request, int $order_id)
    {
        
        $gateway = $request->input('payment_method');
        $payment = $this->paymentService->processPayment($order_id, $gateway);
        if (isset($payment['error'])) {
            return $this->errorResponse('payment processing failed', $payment['status'],['payment_error' => $payment['error']]);
        }
        return $this->successResponse(new PaymentResource($payment['status']), 'Payment processed successfully', 201);
    }

    public function index(Request $request)
    {
        $filters = $request->only(['order_id', 'per_page']);

        $payments = $this->paymentService->getAllPayments($filters);

        return $this->successResponse(
            PaymentResource::collection($payments)->response()->getData(true),
            'Payments retrieved successfully',
            200
        );
    }

    public function show(string $reference_id)
    {
        $payment = $this->paymentService->getPaymentByReferenceId($reference_id);
                if (isset($payment['error'])) {
            return $this->errorResponse('payment processing failed', $payment['status'],['payment_error' => $payment['error']]);
        }
        return $this->successResponse(new PaymentResource($payment) , 'Payment retrieved successfully' , 200);
    }
}
