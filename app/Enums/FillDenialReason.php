<?php

namespace App\Enums;

enum FillDenialReason: string
{
    case VehicleNotFound = 'vehicle_not_found';
    case VehicleNotApproved = 'vehicle_not_approved';
    case CooldownActive = 'cooldown_active';

    public function label(): string
    {
        return match ($this) {
            self::VehicleNotFound => __('Vehicle not found'),
            self::VehicleNotApproved => __('Vehicle not approved'),
            self::CooldownActive => __('Cooldown not ready'),
        };
    }
}
