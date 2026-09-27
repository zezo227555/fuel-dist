<?php

namespace App\Http\Requests\Station;

use App\Support\PlateNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LookupVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('access.station-desk') ?? false;
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
            'station_id' => ['required', 'integer', Rule::exists('stations', 'id')->where('is_active', true)],
            'plate_number' => ['required', 'string', 'max:30'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'station_id' => __('Station'),
            'plate_number' => __('Plate number'),
        ];
    }
}
