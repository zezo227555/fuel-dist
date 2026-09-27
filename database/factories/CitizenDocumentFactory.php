<?php

namespace Database\Factories;

use App\Models\CitizenDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CitizenDocument>
 */
class CitizenDocumentFactory extends Factory
{
    protected $model = CitizenDocument::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->citizen(),
            'type' => 'national_id',
            'disk' => 'local',
            'path' => 'citizen-documents/example.jpg',
            'original_name' => 'example.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 1024,
        ];
    }
}
