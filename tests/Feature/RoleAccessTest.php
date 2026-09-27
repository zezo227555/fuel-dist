<?php

use App\Models\User;

test('supervisor can open staff dashboard and station desk', function () {
    $user = User::factory()->supervisor()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('station.desk'))
        ->assertOk();
});

test('reviewer can open staff dashboard but not station desk', function () {
    $user = User::factory()->reviewer()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('station.desk'))
        ->assertForbidden();
});

test('attendant can open station desk but not staff dashboard', function () {
    $user = User::factory()->attendant()->create();

    $this->actingAs($user)
        ->get(route('station.desk'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});

test('citizen cannot open staff dashboard or station desk', function () {
    $user = User::factory()->citizen()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('station.desk'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('citizen.home'))
        ->assertOk();
});

test('auditor can open staff dashboard but not station desk', function () {
    $user = User::factory()->auditor()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('station.desk'))
        ->assertForbidden();
});
