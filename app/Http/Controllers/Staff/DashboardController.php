<?php

namespace App\Http\Controllers\Staff;

use App\Enums\CooldownChangeStatus;
use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Models\CooldownChangeRequest;
use App\Models\FillUp;
use App\Models\FillUpDenial;
use App\Models\Station;
use App\Models\Vehicle;
use App\Support\Cooldown;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(auth()->user()?->can('access.staff-panel'), 403);

        $today = now()->startOfDay();

        $pendingReviews = Vehicle::query()
            ->where('status', VehicleStatus::Pending)
            ->count();

        $fillsToday = FillUp::query()
            ->where('filled_at', '>=', $today)
            ->count();

        $denialsToday = FillUpDenial::query()
            ->where('attempted_at', '>=', $today)
            ->count();

        $approvedVehicles = Vehicle::query()
            ->where('status', VehicleStatus::Approved)
            ->count();

        $pendingVehicles = Vehicle::query()
            ->with('owner')
            ->where('status', VehicleStatus::Pending)
            ->latest()
            ->limit(5)
            ->get();

        $recentFillUps = FillUp::query()
            ->with(['vehicle', 'station'])
            ->latest('filled_at')
            ->limit(5)
            ->get();

        $pendingCooldownChange = CooldownChangeRequest::query()
            ->with('proposer')
            ->where('status', CooldownChangeStatus::Pending)
            ->latest()
            ->first();

        return view('dashboard', [
            'pendingReviews' => $pendingReviews,
            'fillsToday' => $fillsToday,
            'denialsToday' => $denialsToday,
            'approvedVehicles' => $approvedVehicles,
            'activeStations' => Station::query()->active()->count(),
            'cooldownLabel' => Cooldown::periodDaysLabel(Cooldown::periodHours()),
            'pendingVehicles' => $pendingVehicles,
            'recentFillUps' => $recentFillUps,
            'pendingCooldownChange' => $pendingCooldownChange,
        ]);
    }
}
