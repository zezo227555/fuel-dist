<?php

namespace App\Http\Requests\Profile;

use App\Enums\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCitizenIdentityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCitizen() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($userId),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique(User::class, 'phone')->ignore($userId),
            ],
            'national_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique(User::class, 'national_id')->ignore($userId),
            ],
            'passport_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique(User::class, 'passport_number')->ignore($userId),
            ],
            'national_id_image' => ['nullable', 'image', 'max:5120'],
            'passport_image' => ['nullable', 'image', 'max:5120'],
            'selfie_image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'phone' => __('Phone'),
            'national_id' => __('National ID'),
            'passport_number' => __('Passport number'),
            'national_id_image' => DocumentType::NationalId->label(),
            'passport_image' => DocumentType::Passport->label(),
            'selfie_image' => DocumentType::Selfie->label(),
        ];
    }
}
