<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\OrderItem;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'status' => OrderStatusEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function canBeDeleted(): bool
    {
        return $this->payments()->count() === 0;
    }

    public function canProcessPayment(): bool
    {
        return $this->status === OrderStatusEnum::CONFIRMED;
    }
    
}
