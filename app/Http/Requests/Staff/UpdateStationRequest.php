<?php

namespace App\Http\Requests\Staff;

use App\Enums\AppRole;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateStationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Station $station */
        $station = $this->route('station');

        return $this->user()?->can('update', $station) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'map_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'attendant_ids' => ['nullable', 'array'],
            'attendant_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $ids = array_values(array_unique(array_map('intval', (array) $this->input('attendant_ids', []))));

            if ($ids === []) {
                return;
            }

            $validCount = User::query()
                ->role(AppRole::Attendant)
                ->whereIn('id', $ids)
                ->count();

            if ($validCount !== count($ids)) {
                $validator->errors()->add('attendant_ids', __('Only attendants can be assigned to a station.'));
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('Station name'),
            'address' => __('Address'),
            'latitude' => __('Latitude'),
            'longitude' => __('Longitude'),
            'map_url' => __('Map link'),
            'attendant_ids' => __('Attendants'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'attendant_ids' => array_values(array_filter((array) $this->input('attendant_ids', []))),
        ]);
    }

    /**
     * @return list<int>
     */
    public function attendantIds(): array
    {
        return array_values(array_unique(array_map('intval', (array) $this->validated('attendant_ids', []))));
    }
}
