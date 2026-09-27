<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('My vehicles') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('My vehicles')"
        :description="__('Submit a vehicle for review. Only approved vehicles can receive fuel later.')"
    >
        <x-slot name="actions">
            <a
                href="{{ route('citizen.vehicles.create') }}"
                class="ui-focus inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-md border-2 border-transparent bg-brand px-5 py-3 text-base font-bold text-white hover:bg-brand-hover sm:w-auto"
            >
                <x-ui.icon name="plus" size="sm" />
                {{ __('Add vehicle') }}
            </a>
        </x-slot>
    </x-ui.page-header>

    @if ($vehicles->isEmpty())
        <x-ui.empty-state
            icon="truck"
            :title="__('No vehicles yet')"
            :description="__('Add your first vehicle and upload clear photos for review.')"
        >
            <x-slot name="action">
                <a
                    href="{{ route('citizen.vehicles.create') }}"
                    class="ui-focus inline-flex min-h-12 items-center justify-center gap-2 rounded-md border-2 border-transparent bg-brand px-5 py-3 text-base font-bold text-white hover:bg-brand-hover"
                >
                    <x-ui.icon name="plus" size="sm" />
                    {{ __('Add vehicle') }}
                </a>
            </x-slot>
        </x-ui.empty-state>
    @else
        <x-ui.card :padding="false">
            <x-ui.table class="rounded-none border-0">
                <x-slot name="head">
                    <x-ui.th>{{ __('Plate') }}</x-ui.th>
                    <x-ui.th>{{ __('Vehicle type') }}</x-ui.th>
                    <x-ui.th>{{ __('Status') }}</x-ui.th>
                    <x-ui.th align="end">{{ __('When') }}</x-ui.th>
                </x-slot>

                @foreach ($vehicles as $vehicle)
                    <tr>
                        <x-ui.td>
                            <a href="{{ route('citizen.vehicles.show', $vehicle) }}" class="font-bold text-brand underline dark:text-brand-dark" dir="ltr">
                                {{ $vehicle->plate_display }}
                            </a>
                        </x-ui.td>
                        <x-ui.td>{{ $vehicle->type->label() }}</x-ui.td>
                        <x-ui.td>
                            <x-ui.badge :tone="$vehicle->status->badgeTone()">{{ $vehicle->status->label() }}</x-ui.badge>
                        </x-ui.td>
                        <x-ui.td align="end">{{ $vehicle->created_at->diffForHumans() }}</x-ui.td>
                    </tr>
                @endforeach
            </x-ui.table>
        </x-ui.card>

        <div class="mt-4">
            {{ $vehicles->links() }}
        </div>
    @endif
</x-app-layout>
