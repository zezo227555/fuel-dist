<?php

namespace App\Enums;

enum ReviewDecision: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Approved => __('Approved'),
            self::Rejected => __('Rejected'),
        };
    }
}
