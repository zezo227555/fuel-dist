<?php

namespace App\Enums;

enum VehicleType: string
{
    case Private = 'private';
    case Taxi = 'taxi';
    case Commercial = 'commercial';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Private => __('Private'),
            self::Taxi => __('Taxi'),
            self::Commercial => __('Commercial'),
            self::Other => __('Other'),
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
