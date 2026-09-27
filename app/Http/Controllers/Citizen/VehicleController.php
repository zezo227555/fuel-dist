<?php

namespace App\Http\Controllers\Citizen;

use App\Actions\StoreVehicleImage;
use App\Enums\VehicleImageType;
use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Citizen\StoreVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Vehicle::class);

        $vehicles = $request->user()
            ->vehicles()
            ->latest()
            ->paginate(10);

        return view('citizen.vehicles.index', [
            'vehicles' => $vehicles,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Vehicle::class);

        return view('citizen.vehicles.create');
    }

    public function store(StoreVehicleRequest $request, StoreVehicleImage $storeImage): RedirectResponse
    {
        $vehicle = DB::transaction(function () use ($request, $storeImage): Vehicle {
            $vehicle = $request->user()->vehicles()->create([
                'plate_display' => $request->string('plate_display')->toString(),
                'plate_number' => $request->string('plate_number')->toString(),
                'type' => $request->string('type')->toString(),
                'status' => VehicleStatus::Pending,
            ]);

            $storeImage->handle($vehicle, VehicleImageType::Front, $request->file('front_image'));
            $storeImage->handle($vehicle, VehicleImageType::Rear, $request->file('rear_image'));
            $storeImage->handle($vehicle, VehicleImageType::Side, $request->file('side_image'));

            return $vehicle;
        });

        return redirect()
            ->route('citizen.vehicles.show', $vehicle)
            ->with('success', __('Vehicle submitted for review.'));
    }

    public function show(Request $request, Vehicle $vehicle): View
    {
        $this->authorize('view', $vehicle);

        $vehicle->load(['images', 'reviewLogs.reviewer']);

        return view('citizen.vehicles.show', [
            'vehicle' => $vehicle,
        ]);
    }
}
