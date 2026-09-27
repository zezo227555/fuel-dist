<?php

namespace App\Http\Requests\Staff;

use App\Enums\ReviewDecision;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Vehicle $vehicle */
        $vehicle = $this->route('vehicle');

        return $this->user()?->can('review', $vehicle) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::enum(ReviewDecision::class)],
            'notes' => [
                Rule::requiredIf(fn () => $this->input('decision') === ReviewDecision::Rejected->value),
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'decision' => __('Decision'),
            'notes' => __('Notes'),
        ];
    }
}
