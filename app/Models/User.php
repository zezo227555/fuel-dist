<?php

namespace App\Models;

use App\Enums\AppRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'national_id',
    'passport_number',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasMany<CitizenDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(CitizenDocument::class);
    }

    /**
     * @return HasMany<Vehicle, $this>
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * @return BelongsToMany<Station, $this>
     */
    public function stations(): BelongsToMany
    {
        return $this->belongsToMany(Station::class)->withTimestamps();
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            $user->documents()->get()->each(function (CitizenDocument $document): void {
                $document->deleteFile();
                $document->delete();
            });
        });
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole(AppRole::staffValues());
    }

    public function isCitizen(): bool
    {
        return $this->hasRole(AppRole::Citizen);
    }

    public function homeRouteName(): string
    {
        if ($this->hasRole(AppRole::Attendant) && ! $this->hasRole(AppRole::Supervisor)) {
            return 'station.desk';
        }

        if ($this->can('access.staff-panel')) {
            return 'dashboard';
        }

        if ($this->can('access.station-desk')) {
            return 'station.desk';
        }

        return 'citizen.home';
    }
}
