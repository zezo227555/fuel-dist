<?php

namespace App\Actions;

use App\Models\FillUp;
use App\Models\Vehicle;
use App\Support\Cooldown;
use App\Support\FillEligibilityResult;
use App\Support\PlateNumber;
use Carbon\CarbonInterface;

class CheckFillEligibility
{
    public function handle(?string $plate, ?Vehicle $vehicle = null, ?CarbonInterface $now = null): FillEligibilityResult
    {
        $now ??= now();
        $cooldownHours = Cooldown::periodHours();

        if ($vehicle === null) {
            $normalized = PlateNumber::normalize((string) $plate);

            if ($normalized === '') {
                return FillEligibilityResult::notFound($cooldownHours);
            }

            $vehicle = Vehicle::query()
                ->with('owner')
                ->where('plate_number', $normalized)
                ->first();
        }

        if ($vehicle === null) {
            return FillEligibilityResult::notFound($cooldownHours);
        }

        if (! $vehicle->isApproved()) {
            return FillEligibilityResult::notApproved($vehicle, $cooldownHours);
        }

        /** @var FillUp|null $lastFill */
        $lastFill = FillUp::query()
            ->where('vehicle_id', $vehicle->id)
            ->orderByDesc('filled_at')
            ->first();

        if ($lastFill === null) {
            return FillEligibilityResult::eligible($vehicle, null, $cooldownHours);
        }

        $eligibleAt = $lastFill->filled_at->copy()->addHours($cooldownHours);

        if ($now->lt($eligibleAt)) {
            return FillEligibilityResult::cooldown(
                $vehicle,
                $lastFill->filled_at,
                $eligibleAt,
                $cooldownHours,
            );
        }

        return FillEligibilityResult::eligible($vehicle, $lastFill->filled_at, $cooldownHours);
    }
}
