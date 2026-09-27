<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Stations') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Fuel stations')"
        :description="__('Manage station locations and assigned attendants.')"
    >
        <x-slot name="actions">
            <a
                href="{{ route('staff.stations.create') }}"
                class="ui-focus inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-md border-2 border-transparent bg-brand px-5 py-3 text-base font-bold text-white hover:bg-brand-hover sm:w-auto"
            >
                <x-ui.icon name="plus" size="sm" />
                {{ __('Add station') }}
            </a>
        </x-slot>
    </x-ui.page-header>

    @if ($stations->isEmpty())
        <x-ui.empty-state
            icon="building"
            :title="__('No stations yet')"
            :description="__('Add the first fuel station so citizens can find where to fill up.')"
        >
            <x-slot name="action">
                <a
                    href="{{ route('staff.stations.create') }}"
                    class="ui-focus inline-flex min-h-12 items-center justify-center gap-2 rounded-md border-2 border-transparent bg-brand px-5 py-3 text-base font-bold text-white hover:bg-brand-hover"
                >
                    <x-ui.icon name="plus" size="sm" />
                    {{ __('Add station') }}
                </a>
            </x-slot>
        </x-ui.empty-state>
    @else
        <x-ui.card :padding="false">
            <x-ui.table class="rounded-none border-0">
                <x-slot name="head">
                    <x-ui.th>{{ __('Station name') }}</x-ui.th>
                    <x-ui.th>{{ __('Address') }}</x-ui.th>
                    <x-ui.th>{{ __('Attendants') }}</x-ui.th>
                    <x-ui.th>{{ __('Status') }}</x-ui.th>
                </x-slot>

                @foreach ($stations as $station)
                    <tr>
                        <x-ui.td>
                            <a href="{{ route('staff.stations.show', $station) }}" class="font-bold text-brand underline dark:text-brand-dark">
                                {{ $station->name }}
                            </a>
                        </x-ui.td>
                        <x-ui.td>{{ $station->address ?: '—' }}</x-ui.td>
                        <x-ui.td>{{ $station->attendants_count }}</x-ui.td>
                        <x-ui.td>
                            @if ($station->is_active)
                                <x-ui.badge tone="success">{{ __('Active') }}</x-ui.badge>
                            @else
                                <x-ui.badge tone="blocked">{{ __('Inactive') }}</x-ui.badge>
                            @endif
                        </x-ui.td>
                    </tr>
                @endforeach
            </x-ui.table>
        </x-ui.card>

        <div class="mt-4">
            {{ $stations->links() }}
        </div>
    @endif
</x-app-layout>
