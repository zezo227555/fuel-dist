<?php

namespace Database\Factories;

use App\Enums\VehicleImageType;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleImage>
 */
class VehicleImageFactory extends Factory
{
    protected $model = VehicleImage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'type' => VehicleImageType::Front,
            'disk' => 'local',
            'path' => 'vehicle-images/example.jpg',
            'original_name' => 'example.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 1024,
        ];
    }
}
