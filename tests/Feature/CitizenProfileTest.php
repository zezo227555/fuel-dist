<?php

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

test('citizen can update identity details', function () {
    $user = User::factory()->citizen()->create();

    $response = $this->actingAs($user)->patch(route('profile.identity.update'), [
        'name' => 'اسم جديد',
        'email' => $user->email,
        'phone' => '0922222222',
        'national_id' => $user->national_id,
        'passport_number' => $user->passport_number,
    ]);

    $response->assertRedirect(route('profile.edit'));

    expect($user->fresh()->phone)->toBe('0922222222')
        ->and($user->fresh()->name)->toBe('اسم جديد');
});

test('citizen can replace a document', function () {
    $user = User::factory()->citizen()->create();

    CitizenDocument::query()->create([
        'user_id' => $user->id,
        'type' => DocumentType::Selfie,
        'disk' => 'local',
        'path' => 'citizen-documents/'.$user->id.'/old-selfie.jpg',
        'original_name' => 'old-selfie.jpg',
        'mime_type' => 'image/jpeg',
        'size_bytes' => 10,
    ]);

    Storage::disk('local')->put('citizen-documents/'.$user->id.'/old-selfie.jpg', 'old');

    $this->actingAs($user)->patch(route('profile.identity.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone,
        'national_id' => $user->national_id,
        'passport_number' => $user->passport_number,
        'selfie_image' => UploadedFile::fake()->image('new-selfie.jpg'),
    ])->assertRedirect(route('profile.edit'));

    $document = $user->documents()->where('type', DocumentType::Selfie)->first();

    expect($document)->not->toBeNull()
        ->and($document->original_name)->toBe('new-selfie.jpg')
        ->and(Storage::disk('local')->exists($document->path))->toBeTrue();
});

test('staff cannot use citizen identity update endpoint', function () {
    $user = User::factory()->supervisor()->create();

    $this->actingAs($user)
        ->patch(route('profile.identity.update'), [
            'name' => 'Staff',
            'email' => $user->email,
            'phone' => '0911111111',
            'national_id' => '111',
            'passport_number' => 'P111',
        ])
        ->assertForbidden();
});
