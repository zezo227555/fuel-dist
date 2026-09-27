<?php

use App\Enums\CooldownChangeStatus;
use App\Models\CooldownChangeRequest;
use App\Models\CooldownSetting;
use App\Models\User;
use App\Support\Cooldown;
use Database\Seeders\CooldownSettingSeeder;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->seed(CooldownSettingSeeder::class);
});

test('default cooldown is four days', function () {
    expect(Cooldown::periodHours())->toBe(96);
});

test('supervisor can propose a cooldown change', function () {
    $supervisor = User::factory()->supervisor()->create();

    $this->actingAs($supervisor)
        ->post(route('staff.cooldown.store'), [
            'period_days' => 2,
            'notes' => 'تجربة',
        ])
        ->assertRedirect(route('staff.cooldown.show'));

    $request = CooldownChangeRequest::query()->first();

    expect($request)->not->toBeNull()
        ->and($request->status)->toBe(CooldownChangeStatus::Pending)
        ->and($request->old_period_hours)->toBe(96)
        ->and($request->new_period_hours)->toBe(48)
        ->and(Cooldown::periodHours())->toBe(96);
});

test('proposer cannot approve their own cooldown change', function () {
    $proposer = User::factory()->supervisor()->create();

    $this->actingAs($proposer)
        ->post(route('staff.cooldown.store'), ['period_days' => 2]);

    $change = CooldownChangeRequest::query()->firstOrFail();

    $this->actingAs($proposer)
        ->post(route('staff.cooldown.approve', $change))
        ->assertRedirect(route('staff.cooldown.show'));

    expect($change->fresh()->status)->toBe(CooldownChangeStatus::Pending)
        ->and(Cooldown::periodHours())->toBe(96);
});

test('second supervisor can approve and the setting updates', function () {
    $proposer = User::factory()->supervisor()->create();
    $approver = User::factory()->supervisor()->create();

    $this->actingAs($proposer)
        ->post(route('staff.cooldown.store'), ['period_days' => 2]);

    $change = CooldownChangeRequest::query()->firstOrFail();

    $this->actingAs($approver)
        ->post(route('staff.cooldown.approve', $change))
        ->assertRedirect(route('staff.cooldown.show'));

    expect($change->fresh()->status)->toBe(CooldownChangeStatus::Approved)
        ->and($change->fresh()->reviewed_by)->toBe($approver->id)
        ->and(Cooldown::periodHours())->toBe(48)
        ->and(CooldownSetting::current()->period_hours)->toBe(48);
});

test('second supervisor can reject without changing the setting', function () {
    $proposer = User::factory()->supervisor()->create();
    $reviewer = User::factory()->supervisor()->create();

    $this->actingAs($proposer)
        ->post(route('staff.cooldown.store'), ['period_days' => 2]);

    $change = CooldownChangeRequest::query()->firstOrFail();

    $this->actingAs($reviewer)
        ->post(route('staff.cooldown.reject', $change), ['notes' => 'غير مناسب'])
        ->assertRedirect(route('staff.cooldown.show'));

    expect($change->fresh()->status)->toBe(CooldownChangeStatus::Rejected)
        ->and(Cooldown::periodHours())->toBe(96);
});

test('only one pending cooldown change is allowed', function () {
    $supervisor = User::factory()->supervisor()->create();

    $this->actingAs($supervisor)
        ->post(route('staff.cooldown.store'), ['period_days' => 2])
        ->assertRedirect(route('staff.cooldown.show'));

    $this->actingAs($supervisor)
        ->from(route('staff.cooldown.show'))
        ->post(route('staff.cooldown.store'), ['period_days' => 3])
        ->assertSessionHasErrors('period_days');

    expect(CooldownChangeRequest::query()->count())->toBe(1);
});

test('auditor can view cooldown page but cannot propose', function () {
    $auditor = User::factory()->auditor()->create();

    $this->actingAs($auditor)
        ->get(route('staff.cooldown.show'))
        ->assertOk();

    $this->actingAs($auditor)
        ->post(route('staff.cooldown.store'), ['period_days' => 2])
        ->assertForbidden();
});

test('citizen cannot open cooldown config', function () {
    $citizen = User::factory()->citizen()->create();

    $this->actingAs($citizen)
        ->get(route('staff.cooldown.show'))
        ->assertForbidden();
});
