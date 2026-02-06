<?php

namespace App\Models;

use App\Enums\PaymentGatewayEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'name' => PaymentGatewayEnum::class,

    ];
}
