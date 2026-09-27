<?php

namespace App\Enums;

enum DocumentType: string
{
    case NationalId = 'national_id';
    case Passport = 'passport';
    case Selfie = 'selfie';

    public function label(): string
    {
        return match ($this) {
            self::NationalId => __('National ID image'),
            self::Passport => __('Passport image'),
            self::Selfie => __('Selfie'),
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
