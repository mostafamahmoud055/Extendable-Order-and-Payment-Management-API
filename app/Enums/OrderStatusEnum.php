<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::CANCELED => 'Canceled',
        };
    }
    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
