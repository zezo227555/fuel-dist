<?php

namespace App\Http\Requests\Citizen;

use App\Enums\VehicleType;
use App\Models\Vehicle;
use App\Support\PlateNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Vehicle::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('plate_number')) {
            $raw = trim((string) $this->input('plate_number'));

            $this->merge([
                'plate_display' => $raw,
                'plate_number' => PlateNumber::normalize($raw),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'plate_display' => ['required', 'string', 'max:30'],
            'plate_number' => ['required', 'string', 'max:30', Rule::unique('vehicles', 'plate_number')],
            'type' => ['required', Rule::enum(VehicleType::class)],
            'front_image' => ['required', 'image', 'max:5120'],
            'rear_image' => ['required', 'image', 'max:5120'],
            'side_image' => ['required', 'image', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'plate_number' => __('Plate number'),
            'type' => __('Vehicle type'),
            'front_image' => __('Front image'),
            'rear_image' => __('Rear image'),
            'side_image' => __('Side image'),
        ];
    }
}
