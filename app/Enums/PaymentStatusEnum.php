<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case FAILED = 'failed';
    case SUCCESSFUL = 'successful';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::FAILED => 'Failed',
            self::SUCCESSFUL => 'Successful',
            self::PENDING => 'pending',
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
