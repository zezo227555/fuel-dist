<?php

namespace Database\Factories;

use App\Enums\CooldownChangeStatus;
use App\Models\CooldownChangeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CooldownChangeRequest>
 */
class CooldownChangeRequestFactory extends Factory
{
    protected $model = CooldownChangeRequest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'old_period_hours' => 96,
            'new_period_hours' => 48,
            'proposed_by' => User::factory()->supervisor(),
            'reviewed_by' => null,
            'status' => CooldownChangeStatus::Pending,
            'notes' => null,
            'reviewed_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CooldownChangeStatus::Approved,
            'reviewed_by' => User::factory()->supervisor(),
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CooldownChangeStatus::Rejected,
            'reviewed_by' => User::factory()->supervisor(),
            'reviewed_at' => now(),
            'notes' => 'مرفوض',
        ]);
    }
}
