<?php

namespace Database\Factories;

use App\Models\FillUp;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FillUp>
 */
class FillUpFactory extends Factory
{
    protected $model = FillUp::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory()->approved(),
            'station_id' => Station::factory(),
            'attendant_id' => User::factory()->attendant(),
            'filled_at' => now(),
        ];
    }
}
