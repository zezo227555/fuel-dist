<?php

namespace Database\Factories;

use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Station>
 */
class StationFactory extends Factory
{
    protected $model = Station::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'محطة '.fake()->unique()->city(),
            'address' => fake()->streetAddress().'، سبها',
            'latitude' => fake()->latitude(26.9, 27.2),
            'longitude' => fake()->longitude(14.3, 14.6),
            'map_url' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
