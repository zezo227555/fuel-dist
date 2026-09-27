<?php

use App\Enums\VehicleStatus;
use App\Models\FillUp;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\CooldownSettingSeeder;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->seed(CooldownSettingSeeder::class);
});

test('supervisor dashboard shows live pending and fill counts', function () {
    $supervisor = User::factory()->supervisor()->create();
    $station = Station::factory()->create();
    $attendant = User::factory()->attendant()->create();

    Vehicle::factory()->count(2)->create(['status' => VehicleStatus::Pending]);
    Vehicle::factory()->approved()->create();

    FillUp::factory()->create([
        'station_id' => $station->id,
        'attendant_id' => $attendant->id,
        'filled_at' => now(),
    ]);

    $this->actingAs($supervisor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('Pending registrations'))
        ->assertSee(__('Recent fill-ups'))
        ->assertSee('2', false);
});

test('reviewer dashboard shows review queue but not reports section', function () {
    $reviewer = User::factory()->reviewer()->create();
    Vehicle::factory()->create(['status' => VehicleStatus::Pending]);

    $this->actingAs($reviewer)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('Pending registrations'))
        ->assertDontSee(__('Recent fill-ups'));
});

test('auditor dashboard shows reports overview', function () {
    $auditor = User::factory()->auditor()->create();

    $this->actingAs($auditor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('Reports'))
        ->assertDontSee(__('Pending registrations'));
});
