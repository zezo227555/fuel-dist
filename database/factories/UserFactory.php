<?php

namespace Database\Factories;

use App\Enums\AppRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => null,
            'national_id' => null,
            'passport_number' => null,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withRole(AppRole $role): static
    {
        return $this->afterCreating(function (User $user) use ($role): void {
            if (app()->environment('testing')) {
                $this->ensureRolesSeeded();
            }

            $user->assignRole($role);
        });
    }

    public function citizen(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => fake()->unique()->numerify('09########'),
            'national_id' => fake()->unique()->numerify('##############'),
            'passport_number' => strtoupper(fake()->unique()->bothify('??######')),
        ])->withRole(AppRole::Citizen);
    }

    public function attendant(): static
    {
        return $this->withRole(AppRole::Attendant);
    }

    public function reviewer(): static
    {
        return $this->withRole(AppRole::Reviewer);
    }

    public function supervisor(): static
    {
        return $this->withRole(AppRole::Supervisor);
    }

    public function auditor(): static
    {
        return $this->withRole(AppRole::Auditor);
    }

    protected function ensureRolesSeeded(): void
    {
        if (Role::query()->exists()) {
            return;
        }

        (new RoleAndPermissionSeeder)->run();
    }
}
