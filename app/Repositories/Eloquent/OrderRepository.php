<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = Order::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        $perPage = (int) ($filters['per_page'] ?? 10);

        return $query->with('items', 'payments')
            ->where('user_id', auth()->id())->paginate($perPage);
    }

    public function find(int $id): Order | null
    {
        return Order::with('items', 'payments')->where('user_id', auth()->id())->find($id);
    }


    public function create(array $orderData, array $itemsData): Order
    {
        return DB::transaction(function () use ($orderData, $itemsData) {

            $order = Order::create($orderData);

            foreach ($itemsData as $item) {
                $order->items()->create([
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
            return $order;
        });
    }

    public function update(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {

            $order->update($data);

            if (isset($data['items'])) {
                $order->items()->delete();
                foreach ($data['items'] as $item) {
                    $order->items()->create([
                        'product_name' => $item['product_name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
            }

            return $order;
        });
    }


    public function delete(Order $order): bool
    {
        return $order->delete();
    }
}
