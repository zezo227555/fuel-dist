<?php

namespace App\Models;

use App\Enums\DocumentType;
use Database\Factories\CitizenDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CitizenDocument extends Model
{
    /** @use HasFactory<CitizenDocumentFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
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
            'type' => DocumentType::class,
            'size_bytes' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deleteFile(): void
    {
        Storage::disk($this->disk)->delete($this->path);
    }
}
