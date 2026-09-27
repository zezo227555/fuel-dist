<?php

namespace App\Policies;

use App\Enums\AppPermission;
use App\Models\Station;
use App\Models\User;

class StationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isCitizen()
            || $user->can(AppPermission::ManageStations->value)
            || $user->can(AppPermission::AccessStationDesk->value);
    }

    public function view(User $user, Station $station): bool
    {
        if ($user->can(AppPermission::ManageStations->value)) {
            return true;
        }

        if ($user->isCitizen()) {
            return $station->is_active;
        }

        if ($user->can(AppPermission::AccessStationDesk->value)) {
            return $user->stations()->whereKey($station->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can(AppPermission::ManageStations->value);
    }

    public function update(User $user, Station $station): bool
    {
        return $user->can(AppPermission::ManageStations->value);
    }

    public function delete(User $user, Station $station): bool
    {
        return $user->can(AppPermission::ManageStations->value);
    }
}
