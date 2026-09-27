<?php

use App\Enums\ReviewDecision;
use App\Enums\VehicleStatus;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleReviewLog;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->seed(RoleAndPermissionSeeder::class);
});

test('reviewer can approve a pending vehicle and an audit log is created', function () {
    $reviewer = User::factory()->reviewer()->create();
    $vehicle = Vehicle::factory()->create();

    $this->actingAs($reviewer)
        ->post(route('staff.vehicles.review', $vehicle), [
            'decision' => ReviewDecision::Approved->value,
            'notes' => null,
        ])
        ->assertRedirect(route('staff.vehicles.index'));

    $vehicle->refresh();

    expect($vehicle->status)->toBe(VehicleStatus::Approved)
        ->and($vehicle->reviewed_by)->toBe($reviewer->id)
        ->and(VehicleReviewLog::query()->where('vehicle_id', $vehicle->id)->count())->toBe(1);
});

test('reviewer must provide notes when rejecting', function () {
    $reviewer = User::factory()->reviewer()->create();
    $vehicle = Vehicle::factory()->create();

    $this->actingAs($reviewer)
        ->from(route('staff.vehicles.show', $vehicle))
        ->post(route('staff.vehicles.review', $vehicle), [
            'decision' => ReviewDecision::Rejected->value,
            'notes' => null,
        ])
        ->assertSessionHasErrors('notes');

    expect($vehicle->fresh()->status)->toBe(VehicleStatus::Pending);
});

test('reviewer can reject a pending vehicle with notes', function () {
    $reviewer = User::factory()->reviewer()->create();
    $vehicle = Vehicle::factory()->create();

    $this->actingAs($reviewer)
        ->post(route('staff.vehicles.review', $vehicle), [
            'decision' => ReviewDecision::Rejected->value,
            'notes' => 'الصور غير واضحة',
        ])
        ->assertRedirect(route('staff.vehicles.index'));

    $vehicle->refresh();

    expect($vehicle->status)->toBe(VehicleStatus::Rejected)
        ->and($vehicle->rejection_reason)->toBe('الصور غير واضحة')
        ->and(VehicleReviewLog::query()->where('vehicle_id', $vehicle->id)->value('decision'))->toBe(ReviewDecision::Rejected);
});

test('citizen cannot access review queue', function () {
    $citizen = User::factory()->citizen()->create();

    $this->actingAs($citizen)
        ->get(route('staff.vehicles.index'))
        ->assertForbidden();
});

test('already reviewed vehicle cannot be reviewed again', function () {
    $reviewer = User::factory()->reviewer()->create();
    $vehicle = Vehicle::factory()->approved()->create([
        'reviewed_by' => $reviewer->id,
    ]);

    $this->actingAs($reviewer)
        ->post(route('staff.vehicles.review', $vehicle), [
            'decision' => ReviewDecision::Rejected->value,
            'notes' => 'محاولة ثانية',
        ])
        ->assertForbidden();
});
