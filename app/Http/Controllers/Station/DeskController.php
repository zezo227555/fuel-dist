<?php

namespace App\Http\Controllers\Station;

use App\Actions\CheckFillEligibility;
use App\Actions\RecordFillUp;
use App\Enums\AppPermission;
use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Station\ConfirmFillUpRequest;
use App\Http\Requests\Station\LookupVehicleRequest;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use App\Support\Cooldown;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DeskController extends Controller
{
    public function show(): View
    {
        abort_unless(auth()->user()?->can(AppPermission::AccessStationDesk->value), 403);

        /** @var User $user */
        $user = auth()->user();
        $stations = $this->usableStations($user);

        return view('station.desk', [
            'stations' => $stations,
            'selectedStationId' => old('station_id', $stations->count() === 1 ? $stations->first()->id : null),
            'eligibility' => null,
            'plateDisplay' => old('plate_display', old('plate_number')),
        ]);
    }

    public function lookup(
        LookupVehicleRequest $request,
        CheckFillEligibility $checkFillEligibility,
        RecordFillUp $recordFillUp,
    ): View {
        /** @var User $user */
        $user = $request->user();
        $stations = $this->usableStations($user);
        $station = $this->resolveStation($user, $stations, (int) $request->validated('station_id'));

        $eligibility = $checkFillEligibility->handle($request->validated('plate_number'));

        if (! $eligibility->eligible) {
            $recordFillUp->logDenial(
                $user,
                $station,
                $eligibility,
                $request->validated('plate_number'),
            );
        }

        return view('station.desk', [
            'stations' => $stations,
            'selectedStationId' => $station->id,
            'eligibility' => $eligibility,
            'plateDisplay' => $request->input('plate_display', $request->validated('plate_number')),
            'periodLabel' => Cooldown::periodDaysLabel($eligibility->cooldownHours),
        ]);
    }

    public function confirm(
        ConfirmFillUpRequest $request,
        RecordFillUp $recordFillUp,
    ): RedirectResponse {
        /** @var User $user */
        $user = $request->user();
        $stations = $this->usableStations($user);
        $station = $this->resolveStation($user, $stations, (int) $request->validated('station_id'));
        $vehicle = Vehicle::query()->findOrFail((int) $request->validated('vehicle_id'));

        try {
            $fillUp = $recordFillUp->handle($user, $station, $vehicle);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('station.desk')
                ->withInput([
                    'station_id' => $station->id,
                    'plate_number' => $vehicle->plate_display,
                ])
                ->withErrors($exception->errors());
        }

        return redirect()
            ->route('station.desk')
            ->with('success', __('Fill-up recorded for :plate at :station.', [
                'plate' => $vehicle->plate_display,
                'station' => $station->name,
            ]))
            ->with('last_fill_id', $fillUp->id);
    }

    /**
     * @return Collection<int, Station>
     */
    protected function usableStations(User $user): Collection
    {
        if ($user->hasRole(AppRole::Supervisor)) {
            return Station::query()->active()->orderBy('name')->get();
        }

        return $user->stations()->active()->orderBy('name')->get();
    }

    /**
     * @param  Collection<int, Station>  $stations
     */
    protected function resolveStation(User $user, Collection $stations, int $stationId): Station
    {
        $station = $stations->firstWhere('id', $stationId);

        if ($station === null) {
            throw ValidationException::withMessages([
                'station_id' => __('You are not assigned to this station.'),
            ]);
        }

        return $station;
    }
}
