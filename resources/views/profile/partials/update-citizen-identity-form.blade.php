<section>
    <header>
        <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">
            {{ __('Citizen identity') }}
        </h2>

        <p class="mt-1 text-base text-slate-700 dark:text-slate-300">
            {{ __('Keep your identity details accurate. National ID and passport must stay unique.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.identity.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-2 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', $user->email)" required autocomplete="username" dir="ltr" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-2 block w-full" :value="old('phone', $user->phone)" required autocomplete="tel" dir="ltr" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="national_id" :value="__('National ID')" />
            <x-text-input id="national_id" name="national_id" type="text" class="mt-2 block w-full" :value="old('national_id', $user->national_id)" required dir="ltr" />
            <x-input-error class="mt-2" :messages="$errors->get('national_id')" />
        </div>

        <div>
            <x-input-label for="passport_number" :value="__('Passport number')" />
            <x-text-input id="passport_number" name="passport_number" type="text" class="mt-2 block w-full" :value="old('passport_number', $user->passport_number)" required dir="ltr" />
            <x-input-error class="mt-2" :messages="$errors->get('passport_number')" />
        </div>

        <div class="space-y-4 border-t-2 border-slate-300 pt-6 dark:border-slate-600">
            <p class="text-base font-bold text-ink dark:text-ink-inverse">{{ __('Documents') }}</p>
            <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Leave a file empty to keep the current document.') }}</p>

            @foreach ([
                'national_id_image' => \App\Enums\DocumentType::NationalId,
                'passport_image' => \App\Enums\DocumentType::Passport,
                'selfie_image' => \App\Enums\DocumentType::Selfie,
            ] as $field => $type)
                <div>
                    <x-ui.file-upload :name="$field" :label="$type->label()" />
                    <x-input-error class="mt-2" :messages="$errors->get($field)" />

                    @if ($documents->has($type->value))
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                            {{ __('Current file:') }}
                            <a
                                href="{{ route('citizen.documents.show', $documents[$type->value]) }}"
                                class="font-semibold text-brand underline dark:text-brand-dark"
                                target="_blank"
                                rel="noopener"
                            >
                                {{ $documents[$type->value]->original_name }}
                            </a>
                        </p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="gap-2">
                <x-ui.icon name="check" size="sm" />
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</section>
