<?php

namespace App\Actions;

use App\Enums\CooldownChangeStatus;
use App\Models\CooldownChangeRequest;
use App\Models\CooldownSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReviewCooldownChange
{
    public function approve(CooldownChangeRequest $changeRequest, User $reviewer): CooldownChangeRequest
    {
        return $this->review($changeRequest, $reviewer, CooldownChangeStatus::Approved);
    }

    public function reject(CooldownChangeRequest $changeRequest, User $reviewer, ?string $notes = null): CooldownChangeRequest
    {
        return $this->review($changeRequest, $reviewer, CooldownChangeStatus::Rejected, $notes);
    }

    protected function review(
        CooldownChangeRequest $changeRequest,
        User $reviewer,
        CooldownChangeStatus $decision,
        ?string $notes = null,
    ): CooldownChangeRequest {
        if (! $changeRequest->isPending()) {
            throw new InvalidArgumentException('Only pending cooldown changes can be reviewed.');
        }

        if ($changeRequest->proposed_by === $reviewer->id) {
            throw new InvalidArgumentException('A second person must approve or reject the change.');
        }

        return DB::transaction(function () use ($changeRequest, $reviewer, $decision, $notes): CooldownChangeRequest {
            $locked = CooldownChangeRequest::query()
                ->whereKey($changeRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $locked->isPending()) {
                throw new InvalidArgumentException('Only pending cooldown changes can be reviewed.');
            }

            if ($locked->proposed_by === $reviewer->id) {
                throw new InvalidArgumentException('A second person must approve or reject the change.');
            }

            $locked->update([
                'status' => $decision,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'notes' => $notes ?? $locked->notes,
            ]);

            if ($decision === CooldownChangeStatus::Approved) {
                $setting = CooldownSetting::current();
                $setting->update([
                    'period_hours' => $locked->new_period_hours,
                ]);
            }

            return $locked->fresh(['proposer', 'reviewer']);
        });
    }
}
