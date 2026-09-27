<?php

namespace App\Http\Controllers\Staff;

use App\Actions\ProposeCooldownChange;
use App\Actions\ReviewCooldownChange;
use App\Enums\CooldownChangeStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\ProposeCooldownChangeRequest;
use App\Models\CooldownChangeRequest;
use App\Models\CooldownSetting;
use App\Support\Cooldown;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class CooldownController extends Controller
{
    public function show(): View
    {
        abort_unless(
            auth()->user()?->can('cooldown.manage')
                || auth()->user()?->can('access.audit-reports'),
            403,
        );

        $setting = CooldownSetting::current();
        $pending = CooldownChangeRequest::query()
            ->with('proposer')
            ->where('status', CooldownChangeStatus::Pending)
            ->latest()
            ->first();

        $history = CooldownChangeRequest::query()
            ->with(['proposer', 'reviewer'])
            ->whereIn('status', [
                CooldownChangeStatus::Approved,
                CooldownChangeStatus::Rejected,
            ])
            ->latest()
            ->paginate(15);

        return view('staff.cooldown.show', [
            'setting' => $setting,
            'periodLabel' => Cooldown::periodDaysLabel($setting->period_hours),
            'pending' => $pending,
            'history' => $history,
            'canManage' => auth()->user()?->can('cooldown.manage') ?? false,
        ]);
    }

    public function store(ProposeCooldownChangeRequest $request, ProposeCooldownChange $propose): RedirectResponse
    {
        $propose->handle(
            $request->user(),
            $request->periodHours(),
            $request->string('notes')->toString() ?: null,
        );

        return redirect()
            ->route('staff.cooldown.show')
            ->with('success', __('Cooldown change submitted. A second person must approve it.'));
    }

    public function approve(
        Request $request,
        CooldownChangeRequest $changeRequest,
        ReviewCooldownChange $review,
    ): RedirectResponse {
        abort_unless($request->user()?->can('cooldown.manage'), 403);

        try {
            $review->approve($changeRequest, $request->user());
        } catch (InvalidArgumentException) {
            return redirect()
                ->route('staff.cooldown.show')
                ->with('error', __('A second person must approve or reject the change.'));
        }

        return redirect()
            ->route('staff.cooldown.show')
            ->with('success', __('Cooldown change approved. It applies to fill-ups from now on.'));
    }

    public function reject(
        Request $request,
        CooldownChangeRequest $changeRequest,
        ReviewCooldownChange $review,
    ): RedirectResponse {
        abort_unless($request->user()?->can('cooldown.manage'), 403);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $review->reject(
                $changeRequest,
                $request->user(),
                $validated['notes'] ?? null,
            );
        } catch (InvalidArgumentException) {
            return redirect()
                ->route('staff.cooldown.show')
                ->with('error', __('A second person must approve or reject the change.'));
        }

        return redirect()
            ->route('staff.cooldown.show')
            ->with('success', __('Cooldown change rejected.'));
    }
}
