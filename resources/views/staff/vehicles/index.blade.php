<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Registrations') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Pending vehicle reviews')"
        :description="__('Approve or reject citizen vehicle registrations. Every decision is logged.')"
    />

    @if ($vehicles->isEmpty())
        <x-ui.empty-state
            icon="clipboard"
            :title="__('No pending vehicles')"
            :description="__('New submissions will appear here for review.')"
        />
    @else
        <x-ui.card :padding="false">
            <x-ui.table class="rounded-none border-0">
                <x-slot name="head">
                    <x-ui.th>{{ __('Plate') }}</x-ui.th>
                    <x-ui.th>{{ __('Owner') }}</x-ui.th>
                    <x-ui.th>{{ __('Vehicle type') }}</x-ui.th>
                    <x-ui.th align="end">{{ __('When') }}</x-ui.th>
                </x-slot>

                @foreach ($vehicles as $vehicle)
                    <tr>
                        <x-ui.td>
                            <a href="{{ route('staff.vehicles.show', $vehicle) }}" class="font-bold text-brand underline dark:text-brand-dark" dir="ltr">
                                {{ $vehicle->plate_display }}
                            </a>
                        </x-ui.td>
                        <x-ui.td>{{ $vehicle->owner?->name }}</x-ui.td>
                        <x-ui.td>{{ $vehicle->type->label() }}</x-ui.td>
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
