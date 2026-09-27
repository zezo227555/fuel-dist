<?php

use App\Enums\VehicleStatus;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->seed(RoleAndPermissionSeeder::class);
});

test('citizen can submit a vehicle for review', function () {
    $citizen = User::factory()->citizen()->create();

    $response = $this->actingAs($citizen)->post(route('citizen.vehicles.store'), [
        'plate_number' => 'abc 123',
        'type' => 'private',
        'front_image' => UploadedFile::fake()->image('front.jpg'),
        'rear_image' => UploadedFile::fake()->image('rear.jpg'),
        'side_image' => UploadedFile::fake()->image('side.jpg'),
    ]);

    $vehicle = Vehicle::query()->first();

    $response->assertRedirect(route('citizen.vehicles.show', $vehicle));

    expect($vehicle)->not->toBeNull()
        ->and($vehicle->plate_number)->toBe('ABC123')
        ->and($vehicle->plate_display)->toBe('abc 123')
        ->and($vehicle->status)->toBe(VehicleStatus::Pending)
        ->and($vehicle->images)->toHaveCount(3);
});

test('duplicate normalized plate is rejected', function () {
    $citizen = User::factory()->citizen()->create();

    Vehicle::factory()->create([
        'user_id' => $citizen->id,
        'plate_display' => 'ABC-123',
        'plate_number' => 'ABC-123',
    ]);

    $this->actingAs($citizen)->post(route('citizen.vehicles.store'), [
        'plate_number' => 'abc-123',
        'type' => 'taxi',
        'front_image' => UploadedFile::fake()->image('front.jpg'),
        'rear_image' => UploadedFile::fake()->image('rear.jpg'),
        'side_image' => UploadedFile::fake()->image('side.jpg'),
    ])->assertSessionHasErrors('plate_number');
});

test('citizen cannot view another citizens vehicle', function () {
    $owner = User::factory()->citizen()->create();
    $other = User::factory()->citizen()->create();
    $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)
        ->get(route('citizen.vehicles.show', $vehicle))
        ->assertForbidden();
});

test('staff cannot use citizen vehicle create routes', function () {
    $reviewer = User::factory()->reviewer()->create();

    $this->actingAs($reviewer)
        ->get(route('citizen.vehicles.create'))
        ->assertForbidden();
});
