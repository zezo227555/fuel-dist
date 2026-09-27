<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ $station->name }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="$station->name"
        :description="$station->address"
    >
        <x-slot name="actions">
            <a
                href="{{ route('citizen.stations.index') }}"
                class="ui-focus inline-flex min-h-12 w-full items-center justify-center rounded-md border-2 border-slate-400 bg-white px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800 sm:w-auto"
            >
                {{ __('Back') }}
            </a>
        </x-slot>
    </x-ui.page-header>

    <x-ui.card>
        @if ($station->address)
            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Address') }}</p>
            <p class="mt-1 text-lg font-bold text-ink dark:text-ink-inverse">{{ $station->address }}</p>
        @endif

        @if ($station->mapsLink())
            <div class="mt-4">
                <a
                    href="{{ $station->mapsLink() }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="ui-focus inline-flex min-h-12 items-center justify-center gap-2 rounded-md border-2 border-transparent bg-brand px-5 py-3 text-base font-bold text-white hover:bg-brand-hover"
                >
                    {{ __('Open map') }}
                </a>
            </div>
        @endif
    </x-ui.card>
</x-app-layout>
