<?php
namespace App\Repositories\Contracts;

use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function all(array $filters = []);
    public function find(string $id): Payment | null;
    public function create(array $data): Payment;
}
