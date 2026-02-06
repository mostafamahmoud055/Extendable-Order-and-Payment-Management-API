<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\OrderStatusEnum;
use App\Exceptions\OrderException;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Container\Attributes\DB;

class OrderService
{

    public function __construct(protected OrderRepositoryInterface $orderRepo) {}

    public function createOrder(array $data): Order
    {
        $data['order']['total_amount'] = collect($data['items'] ?? [])
            ->sum(fn($item) => $item['quantity'] * $item['price']);

        $data['order']['status'] = OrderStatusEnum::PENDING;
        $data['order']['user_id'] = auth()->id();

        return $this->orderRepo->create(
            $data['order'],
            $data['items']
        );
    }

    public function updateOrder(int $id, array $data): Order | array
    {
        $order = Order::where('user_id', auth()->id())->find($id);
        if (!$order) {
            return  ['error' => 'Order not found', 'code' => 404];
        }

        if (isset($data['items'])) {
            $data['total_amount'] = collect($data['items'])
                ->sum(fn($item) => $item['quantity'] * $item['price']);
        }

        return $this->orderRepo->update($order, $data);
    }

    public function deleteOrder(int $id): array | bool
    {

        $order = Order::find($id);

        if (!$order) {
            return  ['error' => 'Order Not Found', 'code' => 400];
        }

        if (!$order->canBeDeleted()) {
            return  ['error' => 'Order cannot be deleted because it has associated payments', 'code' => 400];
        }

        return $this->orderRepo->delete($order);
    }

    public function getAllOrders(array $filters = [])
    {
        return $this->orderRepo->all($filters);
    }


    public function getOrderById(int $id): Order | array
    {
        $order = $this->orderRepo->find($id);

        if (!$order) {
            return  ['error' => 'Order not found', 'code' => 404];
        }

        return $order;
    }
}
