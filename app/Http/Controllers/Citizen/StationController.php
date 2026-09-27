<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\View\View;

class StationController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Station::class);

        $stations = Station::query()
            ->active()
            ->orderBy('name')
            ->paginate(20);

        return view('citizen.stations.index', [
            'stations' => $stations,
        ]);
    }

    public function show(Station $station): View
    {
        $this->authorize('view', $station);

        return view('citizen.stations.show', [
            'station' => $station,
        ]);
    }
}
