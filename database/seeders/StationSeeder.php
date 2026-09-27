<?php

namespace Database\Seeders;

use App\Enums\AppRole;
use App\Models\Station;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StationSeeder extends Seeder
{
    public function run(): void
    {
        $attendant = User::query()->updateOrCreate(
            ['email' => 'attendant@fdms.test'],
            [
                'name' => 'موظف المحطة',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $attendant->syncRoles([AppRole::Attendant]);

        $north = Station::query()->updateOrCreate(
            ['name' => 'محطة الشمال'],
            [
                'address' => 'حي المنشية، سبها',
                'latitude' => 27.0377000,
                'longitude' => 14.4283000,
                'map_url' => null,
                'is_active' => true,
            ],
        );

        $south = Station::query()->updateOrCreate(
            ['name' => 'محطة الجنوب'],
            [
                'address' => 'حي المهدية، سبها',
                'latitude' => 26.9950000,
                'longitude' => 14.4410000,
                'map_url' => null,
                'is_active' => true,
            ],
        );

        $north->attendants()->syncWithoutDetaching([$attendant->id]);
        $south->attendants()->syncWithoutDetaching([]);
    }
}
