<?php

namespace Database\Seeders;

use App\Models\CooldownSetting;
use App\Support\Cooldown;
use Illuminate\Database\Seeder;

class CooldownSettingSeeder extends Seeder
{
    public function run(): void
    {
        if (CooldownSetting::query()->exists()) {
            return;
        }

        CooldownSetting::query()->create([
            'period_hours' => Cooldown::DEFAULT_PERIOD_HOURS,
        ]);
    }
}
