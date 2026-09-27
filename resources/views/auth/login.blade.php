<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" dir="ltr" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input
                id="password"
                class="mt-2 block w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                dir="ltr"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4 block">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-2 border-slate-400 text-brand shadow-none focus:ring-brand dark:border-slate-500 dark:bg-surface-dark"
                    name="remember"
                >
                <span class="text-base text-ink dark:text-ink-inverse">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <a
                class="text-base font-semibold text-brand underline hover:text-brand-hover dark:text-brand-dark"
                href="{{ route('register') }}"
            >
                {{ __('Create citizen account') }}
            </a>

            @if (Route::has('password.request'))
                <a
                    class="text-base font-semibold text-brand underline hover:text-brand-hover dark:text-brand-dark"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="gap-2">
                <x-ui.icon name="check" size="sm" />
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
