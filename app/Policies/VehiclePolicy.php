<?php

namespace App\Policies;

use App\Enums\AppPermission;
use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isCitizen() || $user->can(AppPermission::ReviewVehicles->value);
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        if ($user->can(AppPermission::ReviewVehicles->value)) {
            return true;
        }

        return $user->isCitizen() && $user->id === $vehicle->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isCitizen();
    }

    public function review(User $user, Vehicle $vehicle): bool
    {
        return $user->can(AppPermission::ReviewVehicles->value) && $vehicle->isPending();
    }
}
