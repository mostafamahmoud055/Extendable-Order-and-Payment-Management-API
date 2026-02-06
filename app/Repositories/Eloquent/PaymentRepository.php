<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = Payment::query();

        if (!empty($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }

        $query->whereHas('order', fn($q) => $q->where('user_id', auth()->id()));

        $perPage = (int) ($filters['per_page'] ?? 10);

        return $query->with('order')
            ->latest()
            ->paginate($perPage);
    }


    public function find(string $reference_id): Payment | null
    {
        return Payment::with('order.items')->where('reference_id',$reference_id)->first();
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }
}
