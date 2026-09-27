<?php

namespace App\Enums;

enum VehicleImageType: string
{
    case Front = 'front';
    case Rear = 'rear';
    case Side = 'side';

    public function label(): string
    {
        return match ($this) {
            self::Front => __('Front image'),
            self::Rear => __('Rear image'),
            self::Side => __('Side image'),
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
