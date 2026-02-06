<?php
namespace App\Repositories\Contracts;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function all(array $filters = []);
    public function find(int $id): ?Order;
    public function create(array $orderData, array $itemsData): Order;
    public function update(Order $order, array $data): Order;
    public function delete(Order $order): bool;
}
