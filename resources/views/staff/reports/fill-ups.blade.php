<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Fill-ups') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Fill-up report')"
        :description="__('Filter by station and date. Read-only.')"
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
        <form method="GET" action="{{ route('staff.reports.fill-ups') }}" class="grid gap-4 sm:grid-cols-4">
            <div>
                <x-input-label for="station_id" :value="__('Station')" />
                <select id="station_id" name="station_id" class="ui-focus mt-2 block w-full rounded-md border-2 border-slate-400 bg-white px-3 py-3 text-base dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse">
                    <option value="">{{ __('All stations') }}</option>
                    @foreach ($stations as $station)
                        <option value="{{ $station->id }}" @selected((string) $filters['station_id'] === (string) $station->id)>{{ $station->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="from" :value="__('From')" />
                <x-text-input id="from" name="from" type="date" class="mt-2 block w-full" :value="$filters['from']" dir="ltr" />
            </div>
            <div>
                <x-input-label for="to" :value="__('To')" />
                <x-text-input id="to" name="to" type="date" class="mt-2 block w-full" :value="$filters['to']" dir="ltr" />
            </div>
            <div class="flex items-end">
                <x-primary-button class="w-full gap-2">
                    <x-ui.icon name="search" size="sm" />
                    {{ __('Filter') }}
                </x-primary-button>
            </div>
        </form>
        <p class="mt-4 text-sm font-bold text-slate-600 dark:text-slate-400">{{ __('Total') }}: {{ $total }}</p>
    </x-ui.card>

    @if ($fillUps->isEmpty())
        <x-ui.empty-state icon="truck" :title="__('No fill-ups found')" :description="__('Try a wider date range or another station.')" />
    @else
        <x-ui.card :padding="false">
            <x-ui.table class="rounded-none border-0">
                <x-slot name="head">
                    <x-ui.th>{{ __('Plate') }}</x-ui.th>
                    <x-ui.th>{{ __('Owner') }}</x-ui.th>
                    <x-ui.th>{{ __('Station') }}</x-ui.th>
                    <x-ui.th>{{ __('Attendant') }}</x-ui.th>
                    <x-ui.th align="end">{{ __('When') }}</x-ui.th>
                </x-slot>
                @foreach ($fillUps as $fillUp)
                    <tr>
                        <x-ui.td>
                            <a href="{{ route('staff.reports.vehicle-history', ['plate_number' => $fillUp->vehicle?->plate_display]) }}" class="font-bold text-brand underline dark:text-brand-dark" dir="ltr">
                                {{ $fillUp->vehicle?->plate_display }}
                            </a>
                        </x-ui.td>
                        <x-ui.td>{{ $fillUp->vehicle?->owner?->name }}</x-ui.td>
                        <x-ui.td>{{ $fillUp->station?->name }}</x-ui.td>
                        <x-ui.td>{{ $fillUp->attendant?->name }}</x-ui.td>
                        <x-ui.td align="end" dir="ltr">{{ $fillUp->filled_at?->format('Y-m-d H:i') }}</x-ui.td>
                    </tr>
                @endforeach
            </x-ui.table>
        </x-ui.card>
        <div class="mt-4">{{ $fillUps->links() }}</div>
    @endif
</x-app-layout>
