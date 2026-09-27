<?php

namespace App\Models;

use App\Enums\VehicleImageType;
use Database\Factories\VehicleImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VehicleImage extends Model
{
    /** @use HasFactory<VehicleImageFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'vehicle_id',
        'type',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => VehicleImageType::class,
            'size_bytes' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function deleteFile(): void
    {
        Storage::disk($this->disk)->delete($this->path);
    }
}
