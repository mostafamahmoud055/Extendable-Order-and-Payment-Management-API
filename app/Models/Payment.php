<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'status',
        'total_amount',
        'reference_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }
}
