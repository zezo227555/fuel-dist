<?php

namespace App\Models;

use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'plate_display',
        'plate_number',
        'type',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => VehicleType::class,
            'status' => VehicleStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * @return HasMany<VehicleImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    /**
     * @return HasMany<VehicleReviewLog, $this>
     */
    public function reviewLogs(): HasMany
    {
        return $this->hasMany(VehicleReviewLog::class);
    }

    /**
     * @return HasMany<FillUp, $this>
     */
    public function fillUps(): HasMany
    {
        return $this->hasMany(FillUp::class);
    }

    public function isPending(): bool
    {
        return $this->status === VehicleStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === VehicleStatus::Approved;
    }
}
