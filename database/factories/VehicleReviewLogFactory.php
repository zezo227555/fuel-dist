<?php

namespace Database\Factories;

use App\Enums\ReviewDecision;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleReviewLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleReviewLog>
 */
class VehicleReviewLogFactory extends Factory
{
    protected $model = VehicleReviewLog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'reviewer_id' => User::factory()->reviewer(),
            'decision' => ReviewDecision::Approved,
            'notes' => null,
            'created_at' => now(),
        ];
    }
}
