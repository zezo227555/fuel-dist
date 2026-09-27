<?php

namespace App\Models;

use Database\Factories\StationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    /** @use HasFactory<StationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'map_url',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function attendants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * @return HasMany<FillUp, $this>
     */
    public function fillUps(): HasMany
    {
        return $this->hasMany(FillUp::class);
    }

    /**
     * @param  Builder<Station>  $query
     * @return Builder<Station>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function mapsLink(): ?string
    {
        if (filled($this->map_url)) {
            return $this->map_url;
        }

        if ($this->latitude !== null && $this->longitude !== null) {
            return sprintf(
                'https://www.google.com/maps?q=%s,%s',
                $this->latitude,
                $this->longitude,
            );
        }

        return null;
    }
}
