<?php

namespace App\Http\Controllers\Staff;

use App\Enums\ReviewDecision;
use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\ReviewVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VehicleReviewController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Vehicle::class);

        abort_unless($request->user()->can('vehicles.review'), 403);

        $vehicles = Vehicle::query()
            ->with('owner')
            ->where('status', VehicleStatus::Pending)
            ->latest()
            ->paginate(15);

        return view('staff.vehicles.index', [
            'vehicles' => $vehicles,
        ]);
    }

    public function show(Request $request, Vehicle $vehicle): View
    {
        $this->authorize('view', $vehicle);

        abort_unless($request->user()->can('vehicles.review'), 403);

        $vehicle->load(['owner', 'images', 'reviewLogs.reviewer']);

        return view('staff.vehicles.show', [
            'vehicle' => $vehicle,
        ]);
    }

    public function store(ReviewVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $decision = ReviewDecision::from($request->string('decision')->toString());
        $notes = $request->string('notes')->toString() ?: null;

        DB::transaction(function () use ($request, $vehicle, $decision, $notes): void {
            $vehicle->update([
                'status' => $decision === ReviewDecision::Approved
                    ? VehicleStatus::Approved
                    : VehicleStatus::Rejected,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'rejection_reason' => $decision === ReviewDecision::Rejected ? $notes : null,
            ]);

            $vehicle->reviewLogs()->create([
                'reviewer_id' => $request->user()->id,
                'decision' => $decision,
                'notes' => $notes,
                'created_at' => now(),
            ]);
        });

        return redirect()
            ->route('staff.vehicles.index')
            ->with('success', $decision === ReviewDecision::Approved
                ? __('Vehicle approved.')
                : __('Vehicle rejected.'));
    }
}
