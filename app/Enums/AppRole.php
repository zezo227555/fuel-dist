<?php

namespace App\Enums;

enum AppRole: string
{
    case Citizen = 'citizen';
    case Attendant = 'attendant';
    case Reviewer = 'reviewer';
    case Supervisor = 'supervisor';
    case Auditor = 'auditor';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return list<string>
     */
    public static function staffValues(): array
    {
        return [
            self::Attendant->value,
            self::Reviewer->value,
            self::Supervisor->value,
            self::Auditor->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Citizen => __('Citizen'),
            self::Attendant => __('Attendant'),
            self::Reviewer => __('Reviewer'),
            self::Supervisor => __('Supervisor'),
            self::Auditor => __('Auditor'),
        };
    }
}
