<?php

namespace App\Support;

final class WarehouseType
{
    public const WAREHOUSE = 1;
    public const TRANSIT = 2;
    public const PRODUCTION = 3;

    public static function values(): array
    {
        return [self::WAREHOUSE, self::TRANSIT, self::PRODUCTION];
    }

    public static function label(int $value): string
    {
        return [
            self::WAREHOUSE => 'warehouse',
            self::TRANSIT => 'transit',
            self::PRODUCTION => 'production',
        ][$value] ?? 'unknown';
    }
}
