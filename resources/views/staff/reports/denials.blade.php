<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Denied attempts') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Denied attempt log')"
        :description="__('Repeated denials can signal gaming or confusion at the desk.')"
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
        <form method="GET" action="{{ route('staff.reports.denials') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
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
                <x-input-label for="reason" :value="__('Reason')" />
                <select id="reason" name="reason" class="ui-focus mt-2 block w-full rounded-md border-2 border-slate-400 bg-white px-3 py-3 text-base dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse">
                    <option value="">{{ __('All reasons') }}</option>
                    @foreach ($reasons as $reason)
                        <option value="{{ $reason->value }}" @selected($filters['reason'] === $reason->value)>{{ $reason->label() }}</option>
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
    </x-ui.card>

    @if ($denials->isEmpty())
        <x-ui.empty-state icon="ban" :title="__('No denials found')" :description="__('No blocked attempts match these filters.')" />
    @else
        <x-ui.card :padding="false">
            <x-ui.table class="rounded-none border-0">
                <x-slot name="head">
                    <x-ui.th>{{ __('Plate') }}</x-ui.th>
                    <x-ui.th>{{ __('Reason') }}</x-ui.th>
                    <x-ui.th>{{ __('Station') }}</x-ui.th>
                    <x-ui.th>{{ __('Attendant') }}</x-ui.th>
                    <x-ui.th align="end">{{ __('When') }}</x-ui.th>
                </x-slot>
                @foreach ($denials as $denial)
                    <tr>
                        <x-ui.td dir="ltr">
                            @if ($denial->vehicle)
                                <a href="{{ route('staff.reports.vehicle-history', ['plate_number' => $denial->vehicle->plate_display]) }}" class="font-bold text-brand underline dark:text-brand-dark">
                                    {{ $denial->vehicle->plate_display }}
                                </a>
                            @else
                                {{ $denial->plate_number }}
                            @endif
                        </x-ui.td>
                        <x-ui.td><x-ui.badge tone="blocked">{{ $denial->reason->label() }}</x-ui.badge></x-ui.td>
                        <x-ui.td>{{ $denial->station?->name }}</x-ui.td>
                        <x-ui.td>{{ $denial->attendant?->name }}</x-ui.td>
                        <x-ui.td align="end" dir="ltr">{{ $denial->attempted_at?->format('Y-m-d H:i') }}</x-ui.td>
                    </tr>
                @endforeach
            </x-ui.table>
        </x-ui.card>
        <div class="mt-4">{{ $denials->links() }}</div>
    @endif
</x-app-layout>
