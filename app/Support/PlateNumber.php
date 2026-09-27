<?php

namespace App\Support;

class PlateNumber
{
    public static function normalize(string $plate): string
    {
        $plate = trim($plate);
        $plate = preg_replace('/\s+/u', '', $plate) ?? $plate;

        return mb_strtoupper($plate, 'UTF-8');
    }
}
