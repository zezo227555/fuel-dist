<?php

namespace App\Actions;

use App\Enums\DocumentType;
use App\Models\CitizenDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreCitizenDocument
{
    public function handle(User $user, DocumentType $type, UploadedFile $file): CitizenDocument
    {
        return DB::transaction(function () use ($user, $type, $file): CitizenDocument {
            $existing = $user->documents()->where('type', $type)->first();

            if ($existing) {
                $existing->deleteFile();
                $existing->delete();
            }

            $directory = 'citizen-documents/'.$user->id;
            $filename = $type->value.'-'.Str::uuid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs($directory, $filename, 'local');

            return $user->documents()->create([
                'type' => $type,
                'disk' => 'local',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => (string) $file->getClientMimeType(),
                'size_bytes' => $file->getSize() ?: 0,
            ]);
        });
    }
}
