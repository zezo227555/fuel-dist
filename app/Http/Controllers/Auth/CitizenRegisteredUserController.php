<?php

namespace App\Http\Controllers\Auth;

use App\Actions\StoreCitizenDocument;
use App\Enums\AppRole;
use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CitizenRegisterRequest;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class CitizenRegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.citizen-register');
    }

    public function store(CitizenRegisterRequest $request, StoreCitizenDocument $storeDocument): RedirectResponse
    {
        $this->ensureCitizenRoleExists();

        $user = DB::transaction(function () use ($request, $storeDocument): User {
            $user = User::query()->create([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                'phone' => $request->string('phone')->toString(),
                'national_id' => $request->string('national_id')->toString(),
                'passport_number' => $request->string('passport_number')->toString(),
                'password' => $request->string('password')->toString(),
                'email_verified_at' => now(),
            ]);

            $user->assignRole(AppRole::Citizen);

            $storeDocument->handle($user, DocumentType::NationalId, $request->file('national_id_image'));
            $storeDocument->handle($user, DocumentType::Passport, $request->file('passport_image'));
            $storeDocument->handle($user, DocumentType::Selfie, $request->file('selfie_image'));

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('citizen.home')
            ->with('success', __('Your citizen account was created successfully.'));
    }

    protected function ensureCitizenRoleExists(): void
    {
        if (! Role::query()->where('name', AppRole::Citizen->value)->exists()) {
            (new RoleAndPermissionSeeder)->run();
        }
    }
}
