<?php

namespace App\Models;

use App\Enums\FillDenialReason;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FillUpDenial extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'plate_number',
        'vehicle_id',
        'station_id',
        'attendant_id',
        'reason',
        'attempted_at',
        'eligible_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reason' => FillDenialReason::class,
            'attempted_at' => 'datetime',
            'eligible_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo<Station, $this>
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function attendant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attendant_id');
    }
}
