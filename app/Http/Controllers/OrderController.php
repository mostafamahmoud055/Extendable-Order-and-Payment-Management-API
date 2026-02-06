<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\OrderResource;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;

class OrderController extends Controller
{
    use ApiResponseTrait;
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'per_page']);

        $orders = $this->orderService->getAllOrders($filters);

        return $this->successResponse(
            OrderResource::collection($orders)->response()->getData(true),
            'Orders retrieved successfully',
            200
        );
    }
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);

        if (is_array($order) && isset($order['error'])) {
            return $this->errorResponse('Order not found', $order['code'], ['order_id' => $order['error']]);
        }
        return $this->successResponse(new OrderResource($order) , 'Order Retrieved Successfully',200);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {

        $order = $this->orderService->createOrder($request->validated());
        return $this->successResponse(new OrderResource($order), 'Order created successfully', 201);
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {

        $order = $this->orderService->updateOrder($id, $request->validated());

        if (is_array($order) && isset($order['error'])) {
            return $this->errorResponse('Order update failed', $order['code'], ['order_id' => $order['error']]);
        }

        return $this->successResponse(new OrderResource($order), 'Order updated successfully');
    }


    public function destroy(int $id): JsonResponse
    {
        $order = $this->orderService->deleteOrder($id);

        if (is_array($order) && isset($order['error'])) {
            return $this->errorResponse('Order deletion failed', $order['code'], ['order_id' => $order['error']]);
        }

        return $this->successResponse(null, 'Order deleted successfully');
    }
}
