<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Vehicle history') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Vehicle fill-up history')"
        :description="__('Search by plate for dispute resolution.')"
    >
        <x-slot name="actions">
            <a
                href="{{ route('staff.reports.index') }}"
                class="ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-slate-400 px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800"
            >
                {{ __('Back') }}
            </a>
        </x-slot>
    </x-ui.page-header>

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('staff.reports.vehicle-history') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-56 flex-1">
                <x-input-label for="plate_number" :value="__('Plate number')" />
                <x-text-input id="plate_number" name="plate_number" type="text" class="mt-2 block w-full text-xl font-bold" :value="$plateInput" dir="ltr" required autofocus />
            </div>
            <x-primary-button class="gap-2">
                <x-ui.icon name="search" size="sm" />
                {{ __('Search') }}
            </x-primary-button>
        </form>
    </x-ui.card>

    @if ($searched && ! $vehicle)
        <x-ui.empty-state icon="search" :title="__('Vehicle not found')" :description="__('No vehicle matches that plate.')" />
    @elseif ($vehicle)
        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <x-ui.card>
                <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Plate') }}</p>
                <p class="mt-1 text-xl font-bold text-ink dark:text-ink-inverse" dir="ltr">{{ $vehicle->plate_display }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Owner') }}</p>
                <p class="mt-1 text-xl font-bold text-ink dark:text-ink-inverse">{{ $vehicle->owner?->name }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Status') }}</p>
                <div class="mt-2"><x-ui.badge :tone="$vehicle->status->badgeTone()">{{ $vehicle->status->label() }}</x-ui.badge></div>
            </x-ui.card>
        </div>

        <x-ui.card :padding="false" class="mb-6">
            <div class="border-b-2 border-slate-300 px-4 py-3 dark:border-slate-600">
                <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Fill-ups') }}</h2>
            </div>
            @if ($fillUps->isEmpty())
                <div class="p-6"><p class="text-base text-slate-700 dark:text-slate-300">{{ __('No fill-ups for this vehicle.') }}</p></div>
            @else
                <x-ui.table class="rounded-none border-0">
                    <x-slot name="head">
                        <x-ui.th>{{ __('Station') }}</x-ui.th>
                        <x-ui.th>{{ __('Attendant') }}</x-ui.th>
                        <x-ui.th align="end">{{ __('When') }}</x-ui.th>
                    </x-slot>
                    @foreach ($fillUps as $fillUp)
                        <tr>
                            <x-ui.td>{{ $fillUp->station?->name }}</x-ui.td>
                            <x-ui.td>{{ $fillUp->attendant?->name }}</x-ui.td>
                            <x-ui.td align="end" dir="ltr">{{ $fillUp->filled_at?->format('Y-m-d H:i') }}</x-ui.td>
                        </tr>
                    @endforeach
                </x-ui.table>
                <div class="p-4">{{ $fillUps->links() }}</div>
            @endif
        </x-ui.card>

        <x-ui.card :padding="false">
            <div class="border-b-2 border-slate-300 px-4 py-3 dark:border-slate-600">
                <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Denied attempts') }}</h2>
            </div>
            @if ($denials->isEmpty())
                <div class="p-6"><p class="text-base text-slate-700 dark:text-slate-300">{{ __('No denied attempts for this vehicle.') }}</p></div>
            @else
                <x-ui.table class="rounded-none border-0">
                    <x-slot name="head">
                        <x-ui.th>{{ __('Reason') }}</x-ui.th>
                        <x-ui.th>{{ __('Station') }}</x-ui.th>
                        <x-ui.th align="end">{{ __('When') }}</x-ui.th>
                    </x-slot>
                    @foreach ($denials as $denial)
                        <tr>
                            <x-ui.td><x-ui.badge tone="blocked">{{ $denial->reason->label() }}</x-ui.badge></x-ui.td>
                            <x-ui.td>{{ $denial->station?->name }}</x-ui.td>
                            <x-ui.td align="end" dir="ltr">{{ $denial->attempted_at?->format('Y-m-d H:i') }}</x-ui.td>
                        </tr>
                    @endforeach
                </x-ui.table>
                <div class="p-4">{{ $denials->links() }}</div>
            @endif
        </x-ui.card>
    @endif
</x-app-layout>
