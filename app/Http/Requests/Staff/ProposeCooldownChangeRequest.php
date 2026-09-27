<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class ProposeCooldownChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('cooldown.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'period_days' => ['required', 'integer', 'min:1', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'period_days' => __('Cooldown days'),
            'notes' => __('Notes'),
        ];
    }

    public function periodHours(): int
    {
        return (int) $this->validated('period_days') * 24;
    }
}
