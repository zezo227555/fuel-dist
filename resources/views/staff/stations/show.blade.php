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
                href="{{ route('staff.stations.edit', $station) }}"
                class="ui-focus inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-md border-2 border-transparent bg-brand px-5 py-3 text-base font-bold text-white hover:bg-brand-hover sm:w-auto"
            >
                {{ __('Edit') }}
            </a>
            <a
                href="{{ route('staff.stations.index') }}"
                class="ui-focus inline-flex min-h-12 w-full items-center justify-center rounded-md border-2 border-slate-400 bg-white px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800 sm:w-auto"
            >
                {{ __('Back') }}
            </a>
        </x-slot>
    </x-ui.page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <x-ui.card>
            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Status') }}</p>
            <div class="mt-2">
                @if ($station->is_active)
                    <x-ui.badge tone="success">{{ __('Active') }}</x-ui.badge>
                @else
                    <x-ui.badge tone="blocked">{{ __('Inactive') }}</x-ui.badge>
                @endif
            </div>
        </x-ui.card>
        <x-ui.card>
            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Map') }}</p>
            @if ($station->mapsLink())
                <a href="{{ $station->mapsLink() }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block font-bold text-brand underline dark:text-brand-dark" dir="ltr">
                    {{ __('Open map') }}
                </a>
            @else
                <p class="mt-2 text-base text-slate-700 dark:text-slate-300">{{ __('No map link') }}</p>
            @endif
        </x-ui.card>
    </div>

    <x-ui.card>
        <x-slot name="header">
            <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Assigned attendants') }}</h2>
        </x-slot>

        @forelse ($station->attendants as $attendant)
            <div @class(['flex flex-wrap items-center justify-between gap-2 py-3', 'border-t-2 border-slate-200 dark:border-slate-700' => ! $loop->first])>
                <span class="font-bold text-ink dark:text-ink-inverse">{{ $attendant->name }}</span>
                <span class="text-sm text-slate-600 dark:text-slate-400" dir="ltr">{{ $attendant->email }}</span>
            </div>
        @empty
            <p class="text-base text-slate-700 dark:text-slate-300">{{ __('No attendants assigned.') }}</p>
        @endforelse
    </x-ui.card>

    <form
        method="POST"
        action="{{ route('staff.stations.destroy', $station) }}"
        class="mt-6"
        x-data
        @submit.prevent="if (confirm(@js(__('Delete this station?')))) $el.submit()"
    >
        @csrf
        @method('DELETE')
        <x-danger-button type="submit">{{ __('Delete station') }}</x-danger-button>
    </form>
</x-app-layout>
