<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Citizen registration') }}</h1>
        <p class="mt-1 text-base text-slate-700 dark:text-slate-300">
            {{ __('Create your account with your identity documents. Staff accounts are created by the municipality.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <x-ui.form-section :title="__('Account')" :description="__('Login details for this system.')">
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="mt-2 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" dir="ltr" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" class="mt-2 block w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" dir="ltr" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" dir="ltr" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" dir="ltr" />
            </div>
        </x-ui.form-section>

        <x-ui.form-section :title="__('Identity')" :description="__('National ID and passport must be unique.')">
            <div>
                <x-input-label for="national_id" :value="__('National ID')" />
                <x-text-input id="national_id" class="mt-2 block w-full" type="text" name="national_id" :value="old('national_id')" required dir="ltr" />
                <x-input-error :messages="$errors->get('national_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="passport_number" :value="__('Passport number')" />
                <x-text-input id="passport_number" class="mt-2 block w-full" type="text" name="passport_number" :value="old('passport_number')" required dir="ltr" />
                <x-input-error :messages="$errors->get('passport_number')" class="mt-2" />
            </div>
        </x-ui.form-section>

        <x-ui.form-section :title="__('Documents')" :description="__('Images only. Compression will be added later.')">
            <x-ui.file-upload name="national_id_image" :label="__('National ID image')" required />
            <x-input-error :messages="$errors->get('national_id_image')" />

            <x-ui.file-upload name="passport_image" :label="__('Passport image')" required />
            <x-input-error :messages="$errors->get('passport_image')" />

            <x-ui.file-upload name="selfie_image" :label="__('Selfie')" required />
            <x-input-error :messages="$errors->get('selfie_image')" />
        </x-ui.form-section>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
            <a class="text-base font-semibold text-brand underline dark:text-brand-dark" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="gap-2">
                <x-ui.icon name="check" size="sm" />
                {{ __('Create account') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
