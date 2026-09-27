<?php

use App\Enums\FillDenialReason;
use App\Enums\ReviewDecision;
use App\Models\FillUp;
use App\Models\FillUpDenial;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleReviewLog;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('auditor can open reports hub and fill-up report', function () {
    $auditor = User::factory()->auditor()->create();
    $station = Station::factory()->create();
    $vehicle = Vehicle::factory()->approved()->create();
    $attendant = User::factory()->attendant()->create();

    FillUp::factory()->create([
        'vehicle_id' => $vehicle->id,
        'station_id' => $station->id,
        'attendant_id' => $attendant->id,
    ]);

    $this->actingAs($auditor)
        ->get(route('staff.reports.index'))
        ->assertOk()
        ->assertSee(__('Audit & reports'));

    $this->actingAs($auditor)
        ->get(route('staff.reports.fill-ups'))
        ->assertOk()
        ->assertSee($vehicle->plate_display);
});

test('supervisor can open reports', function () {
    $supervisor = User::factory()->supervisor()->create();

    $this->actingAs($supervisor)
        ->get(route('staff.reports.index'))
        ->assertOk();
});

test('auditor can search vehicle history by plate', function () {
    $auditor = User::factory()->auditor()->create();
    $vehicle = Vehicle::factory()->approved()->create([
        'plate_display' => 'REP-55',
        'plate_number' => 'REP-55',
    ]);
    $station = Station::factory()->create();
    $attendant = User::factory()->attendant()->create();

    FillUp::factory()->create([
        'vehicle_id' => $vehicle->id,
        'station_id' => $station->id,
        'attendant_id' => $attendant->id,
    ]);

    $this->actingAs($auditor)
        ->get(route('staff.reports.vehicle-history', ['plate_number' => 'rep-55']))
        ->assertOk()
        ->assertSee('REP-55')
        ->assertSee($vehicle->owner->name);
});

test('auditor can view denials and review logs', function () {
    $auditor = User::factory()->auditor()->create();
    $station = Station::factory()->create(['name' => 'محطة التقرير']);
    $vehicle = Vehicle::factory()->approved()->create();
    $attendant = User::factory()->attendant()->create();
    $reviewer = User::factory()->reviewer()->create();

    FillUpDenial::query()->create([
        'plate_number' => $vehicle->plate_number,
        'vehicle_id' => $vehicle->id,
        'station_id' => $station->id,
        'attendant_id' => $attendant->id,
        'reason' => FillDenialReason::CooldownActive,
        'attempted_at' => now(),
        'eligible_at' => now()->addDay(),
    ]);

    VehicleReviewLog::query()->create([
        'vehicle_id' => $vehicle->id,
        'reviewer_id' => $reviewer->id,
        'decision' => ReviewDecision::Approved,
        'notes' => null,
        'created_at' => now(),
    ]);

    $this->actingAs($auditor)
        ->get(route('staff.reports.denials'))
        ->assertOk()
        ->assertSee('محطة التقرير');

    $this->actingAs($auditor)
        ->get(route('staff.reports.reviews'))
        ->assertOk()
        ->assertSee($reviewer->name);

    $this->actingAs($auditor)
        ->get(route('staff.reports.cooldown'))
        ->assertOk();
});

test('reviewer cannot open audit reports', function () {
    $reviewer = User::factory()->reviewer()->create();

    $this->actingAs($reviewer)
        ->get(route('staff.reports.index'))
        ->assertForbidden();
});

test('citizen cannot open audit reports', function () {
    $citizen = User::factory()->citizen()->create();

    $this->actingAs($citizen)
        ->get(route('staff.reports.index'))
        ->assertForbidden();
});

test('attendant cannot open audit reports', function () {
    $attendant = User::factory()->attendant()->create();

    $this->actingAs($attendant)
        ->get(route('staff.reports.index'))
        ->assertForbidden();
});
