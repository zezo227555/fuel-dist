<?php

namespace App\Enums;

enum AppPermission: string
{
    case AccessStaffPanel = 'access.staff-panel';
    case AccessStationDesk = 'access.station-desk';
    case AccessAuditReports = 'access.audit-reports';
    case ReviewVehicles = 'vehicles.review';
    case ManageStations = 'stations.manage';
    case ManageCooldown = 'cooldown.manage';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
