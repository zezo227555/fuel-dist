<?php

namespace App\Models;

use Database\Factories\FillUpFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FillUp extends Model
{
    /** @use HasFactory<FillUpFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'vehicle_id',
        'station_id',
        'attendant_id',
        'filled_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filled_at' => 'datetime',
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
