<?php

namespace App\Http\Controllers;

use App\Actions\StoreCitizenDocument;
use App\Enums\DocumentType;
use App\Http\Requests\Profile\UpdateCitizenIdentityRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CitizenDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->load('documents');

        return view('profile.edit', [
            'user' => $user,
            'documents' => $user->documents->keyBy(fn (CitizenDocument $document) => $document->type->value),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        if ($request->user()->isCitizen()) {
            return Redirect::route('profile.edit');
        }

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateIdentity(UpdateCitizenIdentityRequest $request, StoreCitizenDocument $storeDocument): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->only([
            'name',
            'email',
            'phone',
            'national_id',
            'passport_number',
        ]));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $uploads = [
            'national_id_image' => DocumentType::NationalId,
            'passport_image' => DocumentType::Passport,
            'selfie_image' => DocumentType::Selfie,
        ];

        foreach ($uploads as $field => $type) {
            if ($request->hasFile($field)) {
                $storeDocument->handle($user, $type, $request->file($field));
            }
        }

        return Redirect::route('profile.edit')->with('success', __('Profile updated.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
