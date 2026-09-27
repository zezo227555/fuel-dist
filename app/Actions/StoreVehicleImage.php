<?php

namespace App\Actions;

use App\Enums\VehicleImageType;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreVehicleImage
{
    public function handle(Vehicle $vehicle, VehicleImageType $type, UploadedFile $file): VehicleImage
    {
        return DB::transaction(function () use ($vehicle, $type, $file): VehicleImage {
            $existing = $vehicle->images()->where('type', $type)->first();

            if ($existing) {
                $existing->deleteFile();
                $existing->delete();
            }

            $directory = 'vehicle-images/'.$vehicle->id;
            $filename = $type->value.'-'.Str::uuid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs($directory, $filename, 'local');

            return $vehicle->images()->create([
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
