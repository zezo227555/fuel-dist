<?php

namespace App\Actions;

use App\Enums\CooldownChangeStatus;
use App\Models\CooldownChangeRequest;
use App\Models\CooldownSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProposeCooldownChange
{
    public function handle(User $proposer, int $newPeriodHours, ?string $notes = null): CooldownChangeRequest
    {
        return DB::transaction(function () use ($proposer, $newPeriodHours, $notes): CooldownChangeRequest {
            $pendingExists = CooldownChangeRequest::query()
                ->where('status', CooldownChangeStatus::Pending)
                ->lockForUpdate()
                ->exists();

            if ($pendingExists) {
                throw ValidationException::withMessages([
                    'period_days' => __('A cooldown change is already waiting for a second approval.'),
                ]);
            }

            $currentHours = CooldownSetting::current()->period_hours;

            if ($newPeriodHours === $currentHours) {
                throw ValidationException::withMessages([
                    'period_days' => __('The new cooldown must differ from the current value.'),
                ]);
            }

            return CooldownChangeRequest::query()->create([
                'old_period_hours' => $currentHours,
                'new_period_hours' => $newPeriodHours,
                'proposed_by' => $proposer->id,
                'status' => CooldownChangeStatus::Pending,
                'notes' => $notes,
            ]);
        });
    }
}
