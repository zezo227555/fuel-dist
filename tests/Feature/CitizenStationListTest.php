<?php

use App\Models\Station;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('citizen can list active stations', function () {
    $citizen = User::factory()->citizen()->create();
    Station::factory()->create(['name' => 'محطة ظاهرة']);
    Station::factory()->inactive()->create(['name' => 'محطة مخفية']);

    $this->actingAs($citizen)
        ->get(route('citizen.stations.index'))
        ->assertOk()
        ->assertSee('محطة ظاهرة')
        ->assertDontSee('محطة مخفية');
});

test('citizen can view an active station', function () {
    $citizen = User::factory()->citizen()->create();
    $station = Station::factory()->create(['name' => 'محطة سبها']);

    $this->actingAs($citizen)
        ->get(route('citizen.stations.show', $station))
        ->assertOk()
        ->assertSee('محطة سبها');
});

test('citizen cannot view an inactive station', function () {
    $citizen = User::factory()->citizen()->create();
    $station = Station::factory()->inactive()->create();

    $this->actingAs($citizen)
        ->get(route('citizen.stations.show', $station))
        ->assertForbidden();
});

test('guest cannot list stations', function () {
    $this->get(route('citizen.stations.index'))
        ->assertRedirect(route('login'));
});
