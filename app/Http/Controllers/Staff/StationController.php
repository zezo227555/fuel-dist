<?php

namespace App\Http\Controllers\Staff;

use App\Enums\AppRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreStationRequest;
use App\Http\Requests\Staff\UpdateStationRequest;
use App\Models\Station;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StationController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Station::class);
        abort_unless(auth()->user()?->can('stations.manage'), 403);

        $stations = Station::query()
            ->withCount('attendants')
            ->latest()
            ->paginate(15);

        return view('staff.stations.index', [
            'stations' => $stations,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Station::class);

        return view('staff.stations.create', [
            'attendants' => $this->attendantOptions(),
        ]);
    }

    public function store(StoreStationRequest $request): RedirectResponse
    {
        $station = Station::query()->create($request->safe()->except('attendant_ids'));
        $station->attendants()->sync($request->attendantIds());

        return redirect()
            ->route('staff.stations.show', $station)
            ->with('success', __('Station created.'));
    }

    public function show(Station $station): View
    {
        $this->authorize('view', $station);
        abort_unless(auth()->user()?->can('stations.manage'), 403);

        $station->load('attendants');

        return view('staff.stations.show', [
            'station' => $station,
        ]);
    }

    public function edit(Station $station): View
    {
        $this->authorize('update', $station);

        $station->load('attendants');

        return view('staff.stations.edit', [
            'station' => $station,
            'attendants' => $this->attendantOptions(),
            'selectedAttendantIds' => $station->attendants->pluck('id')->all(),
        ]);
    }

    public function update(UpdateStationRequest $request, Station $station): RedirectResponse
    {
        $station->update($request->safe()->except('attendant_ids'));
        $station->attendants()->sync($request->attendantIds());

        return redirect()
            ->route('staff.stations.show', $station)
            ->with('success', __('Station updated.'));
    }

    public function destroy(Station $station): RedirectResponse
    {
        $this->authorize('delete', $station);

        $station->attendants()->detach();
        $station->delete();

        return redirect()
            ->route('staff.stations.index')
            ->with('success', __('Station deleted.'));
    }

    /**
     * @return Collection<int, User>
     */
    protected function attendantOptions()
    {
        return User::query()
            ->role(AppRole::Attendant)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
