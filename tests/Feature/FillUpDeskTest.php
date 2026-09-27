<?php

use App\Actions\RecordFillUp;
use App\Enums\FillDenialReason;
use App\Models\FillUp;
use App\Models\FillUpDenial;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\CooldownSettingSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->seed(CooldownSettingSeeder::class);
});

test('attendant can look up an eligible approved vehicle', function () {
    $attendant = User::factory()->attendant()->create();
    $station = Station::factory()->create();
    $station->attendants()->attach($attendant);
    $vehicle = Vehicle::factory()->approved()->create([
        'plate_display' => 'ABC-123',
        'plate_number' => 'ABC-123',
    ]);

    $this->actingAs($attendant)
        ->post(route('station.lookup'), [
            'station_id' => $station->id,
            'plate_number' => 'abc-123',
        ])
        ->assertOk()
        ->assertSee('ABC-123')
        ->assertSee(__('Eligible'));

    expect(FillUpDenial::query()->count())->toBe(0);
});

test('lookup of a vehicle in cooldown logs a denial', function () {
    $attendant = User::factory()->attendant()->create();
    $station = Station::factory()->create();
    $station->attendants()->attach($attendant);
    $vehicle = Vehicle::factory()->approved()->create([
        'plate_display' => 'XYZ-9',
        'plate_number' => 'XYZ-9',
    ]);

    FillUp::factory()->create([
        'vehicle_id' => $vehicle->id,
        'station_id' => $station->id,
        'attendant_id' => $attendant->id,
        'filled_at' => now()->subDay(),
    ]);

    $this->actingAs($attendant)
        ->post(route('station.lookup'), [
            'station_id' => $station->id,
            'plate_number' => 'XYZ-9',
        ])
        ->assertOk()
        ->assertSee(__('Cooldown not ready'));

    expect(FillUpDenial::query()->where('reason', FillDenialReason::CooldownActive)->count())->toBe(1);
});

test('attendant can confirm a fill-up once then is blocked', function () {
    $attendant = User::factory()->attendant()->create();
    $station = Station::factory()->create();
    $station->attendants()->attach($attendant);
    $vehicle = Vehicle::factory()->approved()->create();

    $this->actingAs($attendant)
        ->post(route('station.fill'), [
            'station_id' => $station->id,
            'vehicle_id' => $vehicle->id,
        ])
        ->assertRedirect(route('station.desk'));

    expect(FillUp::query()->where('vehicle_id', $vehicle->id)->count())->toBe(1);

    $this->actingAs($attendant)
        ->post(route('station.fill'), [
            'station_id' => $station->id,
            'vehicle_id' => $vehicle->id,
        ])
        ->assertRedirect(route('station.desk'))
        ->assertSessionHasErrors('plate_number');

    expect(FillUp::query()->where('vehicle_id', $vehicle->id)->count())->toBe(1)
        ->and(FillUpDenial::query()->where('reason', FillDenialReason::CooldownActive)->count())->toBe(1);
});

test('double confirm under lock only records one fill-up', function () {
    $attendant = User::factory()->attendant()->create();
    $station = Station::factory()->create();
    $station->attendants()->attach($attendant);
    $vehicle = Vehicle::factory()->approved()->create();
    $recorder = app(RecordFillUp::class);

    $recorder->handle($attendant, $station, $vehicle);

    expect(fn () => $recorder->handle($attendant, $station, $vehicle))
        ->toThrow(ValidationException::class);

    expect(FillUp::query()->where('vehicle_id', $vehicle->id)->count())->toBe(1);
});

test('attendant cannot use an unassigned station', function () {
    $attendant = User::factory()->attendant()->create();
    $assigned = Station::factory()->create();
    $other = Station::factory()->create();
    $assigned->attendants()->attach($attendant);
    $vehicle = Vehicle::factory()->approved()->create();

    $this->actingAs($attendant)
        ->post(route('station.fill'), [
            'station_id' => $other->id,
            'vehicle_id' => $vehicle->id,
        ])
        ->assertSessionHasErrors('station_id');

    expect(FillUp::query()->count())->toBe(0);
});

test('unknown plate lookup is denied and logged', function () {
    $attendant = User::factory()->attendant()->create();
    $station = Station::factory()->create();
    $station->attendants()->attach($attendant);

    $this->actingAs($attendant)
        ->post(route('station.lookup'), [
            'station_id' => $station->id,
            'plate_number' => 'NOPE-1',
        ])
        ->assertOk()
        ->assertSee(__('Vehicle not found'));

    expect(FillUpDenial::query()->where('reason', FillDenialReason::VehicleNotFound)->count())->toBe(1);
});

test('pending vehicle cannot be filled', function () {
    $attendant = User::factory()->attendant()->create();
    $station = Station::factory()->create();
    $station->attendants()->attach($attendant);
    $vehicle = Vehicle::factory()->create(); // pending

    $this->actingAs($attendant)
        ->post(route('station.fill'), [
            'station_id' => $station->id,
            'vehicle_id' => $vehicle->id,
        ])
        ->assertSessionHasErrors('plate_number');

    expect(FillUp::query()->count())->toBe(0)
        ->and(FillUpDenial::query()->where('reason', FillDenialReason::VehicleNotApproved)->count())->toBe(1);
});

test('citizen cannot open the station desk', function () {
    $citizen = User::factory()->citizen()->create();

    $this->actingAs($citizen)
        ->get(route('station.desk'))
        ->assertForbidden();
});
