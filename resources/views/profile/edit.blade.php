<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Profile') }}</h1>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        @if ($user->isCitizen())
            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.update-citizen-identity-form')
                </div>
            </x-ui.card>
        @else
            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </x-ui.card>
        @endif

        <x-ui.card>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
