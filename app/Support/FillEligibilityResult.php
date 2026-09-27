<?php

namespace App\Support;

use App\Enums\FillDenialReason;
use App\Models\Vehicle;
use Carbon\CarbonInterface;

readonly class FillEligibilityResult
{
    public function __construct(
        public bool $eligible,
        public ?Vehicle $vehicle,
        public ?FillDenialReason $denialReason,
        public ?CarbonInterface $lastFilledAt,
        public ?CarbonInterface $eligibleAt,
        public int $cooldownHours,
    ) {}

    public static function notFound(int $cooldownHours): self
    {
        return new self(
            eligible: false,
            vehicle: null,
            denialReason: FillDenialReason::VehicleNotFound,
            lastFilledAt: null,
            eligibleAt: null,
            cooldownHours: $cooldownHours,
        );
    }

    public static function notApproved(Vehicle $vehicle, int $cooldownHours): self
    {
        return new self(
            eligible: false,
            vehicle: $vehicle,
            denialReason: FillDenialReason::VehicleNotApproved,
            lastFilledAt: null,
            eligibleAt: null,
            cooldownHours: $cooldownHours,
        );
    }

    public static function cooldown(
        Vehicle $vehicle,
        CarbonInterface $lastFilledAt,
        CarbonInterface $eligibleAt,
        int $cooldownHours,
    ): self {
        return new self(
            eligible: false,
            vehicle: $vehicle,
            denialReason: FillDenialReason::CooldownActive,
            lastFilledAt: $lastFilledAt,
            eligibleAt: $eligibleAt,
            cooldownHours: $cooldownHours,
        );
    }

    public static function eligible(
        Vehicle $vehicle,
        ?CarbonInterface $lastFilledAt,
        int $cooldownHours,
    ): self {
        return new self(
            eligible: true,
            vehicle: $vehicle,
            denialReason: null,
            lastFilledAt: $lastFilledAt,
            eligibleAt: null,
            cooldownHours: $cooldownHours,
        );
    }
}
