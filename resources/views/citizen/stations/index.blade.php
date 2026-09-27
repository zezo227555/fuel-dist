<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Stations') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Fuel stations')"
        :description="__('Find where you can fill up when your vehicle is eligible.')"
    />

    @if ($stations->isEmpty())
        <x-ui.empty-state
            icon="building"
            :title="__('No stations listed')"
            :description="__('The municipality has not published active stations yet.')"
        />
    @else
        <div class="space-y-3">
            @foreach ($stations as $station)
                <x-ui.card>
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <a href="{{ route('citizen.stations.show', $station) }}" class="text-lg font-bold text-ink underline dark:text-ink-inverse">
                                {{ $station->name }}
                            </a>
                            @if ($station->address)
                                <p class="mt-1 text-base text-slate-700 dark:text-slate-300">{{ $station->address }}</p>
                            @endif
                        </div>
                        @if ($station->mapsLink())
                            <a
                                href="{{ $station->mapsLink() }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="ui-focus inline-flex min-h-12 items-center justify-center gap-2 rounded-md border-2 border-slate-400 px-4 py-2 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800"
                            >
                                <x-ui.icon name="building" size="sm" />
                                {{ __('Map') }}
                            </a>
                        @endif
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $stations->links() }}
        </div>
    @endif
</x-app-layout>
