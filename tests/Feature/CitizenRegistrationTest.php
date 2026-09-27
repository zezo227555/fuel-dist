<?php

use App\Enums\AppRole;
use App\Enums\DocumentType;
use App\Models\CitizenDocument;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->seed(RoleAndPermissionSeeder::class);
});

test('citizen registration screen can be rendered', function () {
    $this->get(route('register'))->assertOk();
});

test('citizens can register with unique identity and documents', function () {
    $response = $this->post(route('register'), citizenRegistrationPayload());

    $response->assertRedirect(route('citizen.home'));
    $this->assertAuthenticated();

    $user = User::query()->where('email', 'citizen@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole(AppRole::Citizen))->toBeTrue()
        ->and($user->national_id)->toBe('11990011223344')
        ->and($user->documents)->toHaveCount(3);
});

test('duplicate national id is rejected', function () {
    User::factory()->citizen()->create([
        'national_id' => '11990011223344',
    ]);

    $response = $this->from(route('register'))
        ->post(route('register'), citizenRegistrationPayload());

    $response->assertSessionHasErrors('national_id');
    $this->assertGuest();
});

test('duplicate passport number is rejected', function () {
    User::factory()->citizen()->create([
        'passport_number' => 'LY123456',
    ]);

    $response = $this->from(route('register'))
        ->post(route('register'), citizenRegistrationPayload([
            'passport_number' => 'LY123456',
        ]));

    $response->assertSessionHasErrors('passport_number');
    $this->assertGuest();
});

test('citizens can view their own documents only', function () {
    $owner = User::factory()->citizen()->create();
    $other = User::factory()->citizen()->create();

    $document = CitizenDocument::query()->create([
        'user_id' => $owner->id,
        'type' => DocumentType::Selfie,
        'disk' => 'local',
        'path' => 'citizen-documents/'.$owner->id.'/selfie.jpg',
        'original_name' => 'selfie.jpg',
        'mime_type' => 'image/jpeg',
        'size_bytes' => 10,
    ]);

    Storage::disk('local')->put($document->path, 'fake-image');

    $this->actingAs($owner)
        ->get(route('citizen.documents.show', $document))
        ->assertOk();

    $this->actingAs($other)
        ->get(route('citizen.documents.show', $document))
        ->assertForbidden();
});

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function citizenRegistrationPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'مواطن تجريبي',
        'email' => 'citizen@example.com',
        'phone' => '0912345678',
        'national_id' => '11990011223344',
        'passport_number' => 'LY123456',
        'password' => 'password',
        'password_confirmation' => 'password',
        'national_id_image' => UploadedFile::fake()->image('national-id.jpg'),
        'passport_image' => UploadedFile::fake()->image('passport.jpg'),
        'selfie_image' => UploadedFile::fake()->image('selfie.jpg'),
    ], $overrides);
}
