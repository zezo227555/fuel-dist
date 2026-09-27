<?php

namespace Database\Factories;

use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use App\Models\User;
use App\Models\Vehicle;
use App\Support\PlateNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $display = strtoupper(fake()->unique()->bothify('??-####'));

        return [
            'user_id' => User::factory()->citizen(),
            'plate_display' => $display,
            'plate_number' => PlateNumber::normalize($display),
            'type' => VehicleType::Private,
            'status' => VehicleStatus::Pending,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'rejection_reason' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VehicleStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VehicleStatus::Rejected,
            'reviewed_at' => now(),
            'rejection_reason' => 'بيانات غير واضحة',
        ]);
    }
}
