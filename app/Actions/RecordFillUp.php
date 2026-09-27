<?php

namespace App\Actions;

use App\Enums\FillDenialReason;
use App\Models\FillUp;
use App\Models\FillUpDenial;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use App\Support\FillEligibilityResult;
use App\Support\PlateNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class RecordFillUp
{
    public function __construct(
        private CheckFillEligibility $checkFillEligibility,
    ) {}

    public function handle(User $attendant, Station $station, Vehicle $vehicle): FillUp
    {
        /** @var FillEligibilityResult|null $eligibility */
        $eligibility = null;

        $fillUp = DB::transaction(function () use ($attendant, $station, $vehicle, &$eligibility): ?FillUp {
            $locked = Vehicle::query()
                ->whereKey($vehicle->id)
                ->lockForUpdate()
                ->firstOrFail();

            $eligibility = $this->checkFillEligibility->handle(null, $locked);

            if (! $eligibility->eligible) {
                return null;
            }

            return FillUp::query()->create([
                'vehicle_id' => $locked->id,
                'station_id' => $station->id,
                'attendant_id' => $attendant->id,
                'filled_at' => now(),
            ]);
        });

        if ($fillUp === null) {
            $this->logDenial(
                $attendant,
                $station,
                $eligibility ?? FillEligibilityResult::notFound(0),
                $vehicle->plate_number,
            );

            throw ValidationException::withMessages([
                'plate_number' => $eligibility?->denialReason?->label() ?? __('Fill-up denied.'),
            ]);
        }

        return $fillUp;
    }

    public function logDenial(
        User $attendant,
        Station $station,
        FillEligibilityResult $eligibility,
        ?string $plateNumber = null,
    ): FillUpDenial {
        $plate = $plateNumber
            ?? $eligibility->vehicle?->plate_number
            ?? '';

        if ($plate === '') {
            throw new RuntimeException('Denial requires a plate number.');
        }

        return FillUpDenial::query()->create([
            'plate_number' => PlateNumber::normalize($plate),
            'vehicle_id' => $eligibility->vehicle?->id,
            'station_id' => $station->id,
            'attendant_id' => $attendant->id,
            'reason' => $eligibility->denialReason ?? FillDenialReason::VehicleNotFound,
            'attempted_at' => now(),
            'eligible_at' => $eligibility->eligibleAt,
        ]);
    }
}
