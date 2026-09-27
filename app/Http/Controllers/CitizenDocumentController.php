<?php

namespace App\Http\Controllers;

use App\Models\CitizenDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CitizenDocumentController extends Controller
{
    public function show(Request $request, CitizenDocument $document): StreamedResponse
    {
        abort_unless($request->user()?->is($document->user), 403);

        abort_unless(Storage::disk($document->disk)->exists($document->path), 404);

        return Storage::disk($document->disk)->response(
            $document->path,
            $document->original_name,
            [
                'Content-Type' => $document->mime_type,
            ],
        );
    }
}
