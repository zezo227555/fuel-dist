<?php

namespace Database\Seeders;

use App\Enums\AppPermission;
use App\Enums\AppRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (AppPermission::cases() as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        foreach (AppRole::cases() as $role) {
            Role::findOrCreate($role->value, 'web');
        }

        Role::findByName(AppRole::Citizen->value, 'web')
            ->syncPermissions([]);

        Role::findByName(AppRole::Attendant->value, 'web')
            ->syncPermissions([
                AppPermission::AccessStationDesk->value,
            ]);

        Role::findByName(AppRole::Reviewer->value, 'web')
            ->syncPermissions([
                AppPermission::AccessStaffPanel->value,
                AppPermission::ReviewVehicles->value,
            ]);

        Role::findByName(AppRole::Supervisor->value, 'web')
            ->syncPermissions([
                AppPermission::AccessStaffPanel->value,
                AppPermission::AccessStationDesk->value,
                AppPermission::AccessAuditReports->value,
                AppPermission::ReviewVehicles->value,
                AppPermission::ManageStations->value,
                AppPermission::ManageCooldown->value,
            ]);

        Role::findByName(AppRole::Auditor->value, 'web')
            ->syncPermissions([
                AppPermission::AccessStaffPanel->value,
                AppPermission::AccessAuditReports->value,
            ]);
    }
}
