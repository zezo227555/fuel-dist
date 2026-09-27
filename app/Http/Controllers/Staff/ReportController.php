<?php

namespace App\Http\Controllers\Staff;

use App\Enums\FillDenialReason;
use App\Enums\ReviewDecision;
use App\Http\Controllers\Controller;
use App\Models\CooldownChangeRequest;
use App\Models\FillUp;
use App\Models\FillUpDenial;
use App\Models\Station;
use App\Models\Vehicle;
use App\Models\VehicleReviewLog;
use App\Support\PlateNumber;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $this->authorizeReports();

        $today = now()->startOfDay();

        return view('staff.reports.index', [
            'fillsToday' => FillUp::query()->where('filled_at', '>=', $today)->count(),
            'denialsToday' => FillUpDenial::query()->where('attempted_at', '>=', $today)->count(),
            'pendingReviews' => Vehicle::query()->where('status', 'pending')->count(),
            'stationsCount' => Station::query()->active()->count(),
            'stationTotals' => Station::query()
                ->withCount('fillUps')
                ->orderByDesc('fill_ups_count')
                ->limit(5)
                ->get(),
        ]);
    }

    public function fillUps(Request $request): View
    {
        $this->authorizeReports();

        $validated = $request->validate([
            'station_id' => ['nullable', 'integer', 'exists:stations,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = FillUp::query()
            ->with(['vehicle.owner', 'station', 'attendant'])
            ->latest('filled_at');

        if (! empty($validated['station_id'])) {
            $query->where('station_id', $validated['station_id']);
        }

        if (! empty($validated['from'])) {
            $query->whereDate('filled_at', '>=', $validated['from']);
        }

        if (! empty($validated['to'])) {
            $query->whereDate('filled_at', '<=', $validated['to']);
        }

        $total = (clone $query)->count();

        return view('staff.reports.fill-ups', [
            'fillUps' => $query->paginate(20)->withQueryString(),
            'stations' => Station::query()->orderBy('name')->get(['id', 'name']),
            'filters' => [
                'station_id' => $validated['station_id'] ?? null,
                'from' => $validated['from'] ?? null,
                'to' => $validated['to'] ?? null,
            ],
            'total' => $total,
        ]);
    }

    public function vehicleHistory(Request $request): View
    {
        $this->authorizeReports();

        $validated = $request->validate([
            'plate_number' => ['nullable', 'string', 'max:30'],
        ]);

        $plate = isset($validated['plate_number'])
            ? PlateNumber::normalize($validated['plate_number'])
            : '';

        $vehicle = null;
        $fillUps = null;
        $denials = null;

        if ($plate !== '') {
            $vehicle = Vehicle::query()
                ->with('owner')
                ->where('plate_number', $plate)
                ->first();

            if ($vehicle) {
                $fillUps = FillUp::query()
                    ->with(['station', 'attendant'])
                    ->where('vehicle_id', $vehicle->id)
                    ->latest('filled_at')
                    ->paginate(15, ['*'], 'fills_page')
                    ->withQueryString();

                $denials = FillUpDenial::query()
                    ->with('station')
                    ->where('vehicle_id', $vehicle->id)
                    ->latest('attempted_at')
                    ->paginate(15, ['*'], 'denials_page')
                    ->withQueryString();
            }
        }

        return view('staff.reports.vehicle-history', [
            'plateInput' => $request->input('plate_number', ''),
            'vehicle' => $vehicle,
            'fillUps' => $fillUps,
            'denials' => $denials,
            'searched' => $plate !== '',
        ]);
    }

    public function denials(Request $request): View
    {
        $this->authorizeReports();

        $validated = $request->validate([
            'station_id' => ['nullable', 'integer', 'exists:stations,id'],
            'reason' => ['nullable', Rule::enum(FillDenialReason::class)],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = FillUpDenial::query()
            ->with(['vehicle.owner', 'station', 'attendant'])
            ->latest('attempted_at');

        if (! empty($validated['station_id'])) {
            $query->where('station_id', $validated['station_id']);
        }

        if (! empty($validated['reason'])) {
            $query->where('reason', $validated['reason']);
        }

        if (! empty($validated['from'])) {
            $query->whereDate('attempted_at', '>=', $validated['from']);
        }

        if (! empty($validated['to'])) {
            $query->whereDate('attempted_at', '<=', $validated['to']);
        }

        return view('staff.reports.denials', [
            'denials' => $query->paginate(20)->withQueryString(),
            'stations' => Station::query()->orderBy('name')->get(['id', 'name']),
            'reasons' => FillDenialReason::cases(),
            'filters' => [
                'station_id' => $validated['station_id'] ?? null,
                'reason' => $validated['reason'] ?? null,
                'from' => $validated['from'] ?? null,
                'to' => $validated['to'] ?? null,
            ],
        ]);
    }

    public function reviews(Request $request): View
    {
        $this->authorizeReports();

        $validated = $request->validate([
            'decision' => ['nullable', Rule::enum(ReviewDecision::class)],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = VehicleReviewLog::query()
            ->with(['vehicle.owner', 'reviewer'])
            ->latest('created_at');

        if (! empty($validated['decision'])) {
            $query->where('decision', $validated['decision']);
        }

        if (! empty($validated['from'])) {
            $query->whereDate('created_at', '>=', $validated['from']);
        }

        if (! empty($validated['to'])) {
            $query->whereDate('created_at', '<=', $validated['to']);
        }

        return view('staff.reports.reviews', [
            'logs' => $query->paginate(20)->withQueryString(),
            'decisions' => ReviewDecision::cases(),
            'filters' => [
                'decision' => $validated['decision'] ?? null,
                'from' => $validated['from'] ?? null,
                'to' => $validated['to'] ?? null,
            ],
        ]);
    }

    public function cooldown(): View
    {
        $this->authorizeReports();

        $history = CooldownChangeRequest::query()
            ->with(['proposer', 'reviewer'])
            ->latest()
            ->paginate(20);

        return view('staff.reports.cooldown', [
            'history' => $history,
        ]);
    }

    protected function authorizeReports(): void
    {
        abort_unless(auth()->user()?->can('access.audit-reports'), 403);
    }
}
