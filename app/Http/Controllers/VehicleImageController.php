<?php

namespace App\Http\Controllers;

use App\Models\VehicleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleImageController extends Controller
{
    public function show(Request $request, VehicleImage $image): StreamedResponse
    {
        $image->loadMissing('vehicle');

        $this->authorize('view', $image->vehicle);

        abort_unless(Storage::disk($image->disk)->exists($image->path), 404);

        return Storage::disk($image->disk)->response(
            $image->path,
            $image->original_name,
            [
                'Content-Type' => $image->mime_type,
            ],
        );
    }
}
