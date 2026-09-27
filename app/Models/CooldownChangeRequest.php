<?php

namespace App\Models;

use App\Enums\CooldownChangeStatus;
use Database\Factories\CooldownChangeRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CooldownChangeRequest extends Model
{
    /** @use HasFactory<CooldownChangeRequestFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'old_period_hours',
        'new_period_hours',
        'proposed_by',
        'reviewed_by',
        'status',
        'notes',
        'reviewed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_period_hours' => 'integer',
            'new_period_hours' => 'integer',
            'status' => CooldownChangeStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === CooldownChangeStatus::Pending;
    }
}
