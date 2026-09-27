<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CooldownSetting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'period_hours',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period_hours' => 'integer',
        ];
    }

    public static function current(): self
    {
        $setting = static::query()->latest('id')->first();

        if ($setting === null) {
            $setting = static::query()->create([
                'period_hours' => 96,
            ]);
        }

        return $setting;
    }
}
