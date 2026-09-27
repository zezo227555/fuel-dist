<?php

namespace App\Http\Requests\Station;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmFillUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('access.station-desk') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'station_id' => ['required', 'integer', Rule::exists('stations', 'id')->where('is_active', true)],
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'station_id' => __('Station'),
            'vehicle_id' => __('Vehicle'),
        ];
    }
}
