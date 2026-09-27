<?php

use App\Models\Station;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('supervisor can create a station and assign an attendant', function () {
    $supervisor = User::factory()->supervisor()->create();
    $attendant = User::factory()->attendant()->create();

    $this->actingAs($supervisor)
        ->post(route('staff.stations.store'), [
            'name' => 'محطة الاختبار',
            'address' => 'سبها',
            'latitude' => '27.0377',
            'longitude' => '14.4283',
            'is_active' => '1',
            'attendant_ids' => [$attendant->id],
        ])
        ->assertRedirect();

    $station = Station::query()->where('name', 'محطة الاختبار')->first();

    expect($station)->not->toBeNull()
        ->and($station->attendants()->pluck('users.id')->all())->toContain($attendant->id);
});

test('supervisor can update station attendants', function () {
    $supervisor = User::factory()->supervisor()->create();
    $attendantA = User::factory()->attendant()->create();
    $attendantB = User::factory()->attendant()->create();
    $station = Station::factory()->create();
    $station->attendants()->attach($attendantA);

    $this->actingAs($supervisor)
        ->put(route('staff.stations.update', $station), [
            'name' => $station->name,
            'address' => $station->address,
            'is_active' => '1',
            'attendant_ids' => [$attendantB->id],
        ])
        ->assertRedirect(route('staff.stations.show', $station));

    expect($station->fresh()->attendants()->pluck('users.id')->all())
        ->toBe([$attendantB->id]);
});

test('cannot assign a non-attendant user to a station', function () {
    $supervisor = User::factory()->supervisor()->create();
    $citizen = User::factory()->citizen()->create();

    $this->actingAs($supervisor)
        ->from(route('staff.stations.create'))
        ->post(route('staff.stations.store'), [
            'name' => 'محطة خاطئة',
            'is_active' => '1',
            'attendant_ids' => [$citizen->id],
        ])
        ->assertSessionHasErrors('attendant_ids');

    expect(Station::query()->where('name', 'محطة خاطئة')->exists())->toBeFalse();
});

test('reviewer cannot manage stations', function () {
    $reviewer = User::factory()->reviewer()->create();

    $this->actingAs($reviewer)
        ->get(route('staff.stations.index'))
        ->assertForbidden();
});

test('citizen cannot open staff station management', function () {
    $citizen = User::factory()->citizen()->create();

    $this->actingAs($citizen)
        ->get(route('staff.stations.index'))
        ->assertForbidden();
});

test('attendant only sees assigned stations on the desk', function () {
    $attendant = User::factory()->attendant()->create();
    $assigned = Station::factory()->create(['name' => 'محطة معيّنة']);
    $other = Station::factory()->create(['name' => 'محطة أخرى']);
    $assigned->attendants()->attach($attendant);

    $this->actingAs($attendant)
        ->get(route('station.desk'))
        ->assertOk()
        ->assertSee('محطة معيّنة')
        ->assertDontSee('محطة أخرى');
});
