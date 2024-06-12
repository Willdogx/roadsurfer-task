<?php

declare(strict_types=1);

namespace App\Enum;

enum DistanceUnit: string
{
    case M = 'm';
    case KM = 'km';

    /**
     * @return string[] values of cases
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}