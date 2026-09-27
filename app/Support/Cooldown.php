<?php

namespace App\Support;

use App\Models\CooldownSetting;

class Cooldown
{
    public const DEFAULT_PERIOD_HOURS = 96;

    public static function periodHours(): int
    {
        return CooldownSetting::current()->period_hours;
    }

    public static function periodDaysLabel(int $hours): string
    {
        if ($hours % 24 === 0) {
            return __(':count days', ['count' => $hours / 24]);
        }

        return __(':count hours', ['count' => $hours]);
    }
}
