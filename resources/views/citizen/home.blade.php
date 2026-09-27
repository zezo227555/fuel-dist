<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Home') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Welcome, :name', ['name' => $user->name])"
        :description="__('Manage your vehicles and identity documents from here.')"
    >
        <x-slot name="actions">
            <a
                href="{{ route('citizen.vehicles.create') }}"
                class="ui-focus inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-md border-2 border-transparent bg-brand px-5 py-3 text-base font-bold text-white hover:bg-brand-hover sm:w-auto"
            >
                <x-ui.icon name="plus" size="sm" />
                {{ __('Add vehicle') }}
            </a>
            <a
                href="{{ route('citizen.stations.index') }}"
                class="ui-focus inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-md border-2 border-slate-400 bg-white px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800 sm:w-auto"
            >
                <x-ui.icon name="building" size="sm" />
                {{ __('Stations') }}
            </a>
            <a
                href="{{ route('profile.edit') }}"
                class="ui-focus inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-md border-2 border-slate-400 bg-white px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800 sm:w-auto"
            >
                <x-ui.icon name="user" size="sm" />
                {{ __('Profile') }}
            </a>
        </x-slot>
    </x-ui.page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <x-ui.card>
            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('National ID') }}</p>
            <p class="mt-1 text-lg font-bold text-ink dark:text-ink-inverse" dir="ltr">{{ $user->national_id }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Vehicles') }}</p>
            <p class="mt-1 text-lg font-bold text-ink dark:text-ink-inverse">{{ $user->vehicles()->count() }}</p>
        </x-ui.card>
    </div>

    <x-ui.card>
        <x-slot name="header">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Recent vehicles') }}</h2>
                <a href="{{ route('citizen.vehicles.index') }}" class="font-bold text-brand underline dark:text-brand-dark">{{ __('View all') }}</a>
            </div>
        </x-slot>

        @forelse ($vehicles as $vehicle)
            <div @class(['flex flex-wrap items-center justify-between gap-2 py-3', 'border-t-2 border-slate-200 dark:border-slate-700' => ! $loop->first])>
                <a href="{{ route('citizen.vehicles.show', $vehicle) }}" class="font-bold text-ink underline dark:text-ink-inverse" dir="ltr">{{ $vehicle->plate_display }}</a>
                <x-ui.badge :tone="$vehicle->status->badgeTone()">{{ $vehicle->status->label() }}</x-ui.badge>
            </div>
        @empty
            <p class="text-base text-slate-700 dark:text-slate-300">{{ __('No vehicles yet') }}</p>
        @endforelse
    </x-ui.card>
</x-app-layout>
