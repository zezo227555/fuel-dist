<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Dashboard') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Staff workspace')"
        :description="__('Overview of registrations, fill-ups, and what needs attention today.')"
    >
        <x-slot name="actions">
            @can('access.station-desk')
                <a
                    href="{{ route('station.desk') }}"
                    class="ui-focus inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-md border-2 border-slate-400 bg-white px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800 sm:w-auto"
                >
                    <x-ui.icon name="building" size="sm" />
                    {{ __('Station desk') }}
                </a>
            @endcan
            @can('vehicles.review')
                <a
                    href="{{ route('staff.vehicles.index') }}"
                    class="ui-focus inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-md border-2 border-transparent bg-brand px-5 py-3 text-base font-bold text-white hover:bg-brand-hover sm:w-auto"
                >
                    <x-ui.icon name="clipboard" size="sm" />
                    {{ __('Registrations') }}
                    @if ($pendingReviews > 0)
                        <span class="rounded bg-white/20 px-2 py-0.5 text-sm">{{ $pendingReviews }}</span>
                    @endif
                </a>
            @endcan
        </x-slot>
    </x-ui.page-header>

    @if ($pendingCooldownChange && auth()->user()->can('cooldown.manage'))
        <div class="mb-6">
            <x-ui.alert type="warning" :title="__('Cooldown change waiting')">
                {{ __('A change from :from to :to needs a second supervisor.', [
                    'from' => \App\Support\Cooldown::periodDaysLabel($pendingCooldownChange->old_period_hours),
                    'to' => \App\Support\Cooldown::periodDaysLabel($pendingCooldownChange->new_period_hours),
                ]) }}
                <a href="{{ route('staff.cooldown.show') }}" class="mt-2 inline-block font-bold underline">{{ __('Open cooldown') }}</a>
            </x-ui.alert>
        </div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat :label="__('Pending reviews')" :value="(string) $pendingReviews" :hint="__('Waiting on documents')" icon="clipboard" />
        <x-ui.stat :label="__('Fills today')" :value="(string) $fillsToday" :hint="__('All stations')" icon="truck" />
        <x-ui.stat :label="__('Denied attempts')" :value="(string) $denialsToday" :hint="__('Today')" icon="ban" />
        <x-ui.stat :label="__('Approved vehicles')" :value="(string) $approvedVehicles" :hint="__('Cooldown').': '.$cooldownLabel" icon="check-circle" />
    </div>

    <div class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @can('vehicles.review')
            <a href="{{ route('staff.vehicles.index') }}" class="rounded-md border-2 border-slate-400 bg-white px-4 py-3 font-bold text-ink hover:bg-slate-50 dark:border-slate-600 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800">
                <span class="flex items-center gap-2">
                    <x-ui.icon name="clipboard" size="sm" />
                    {{ __('Review queue') }}
                </span>
            </a>
        @endcan
        @can('stations.manage')
            <a href="{{ route('staff.stations.index') }}" class="rounded-md border-2 border-slate-400 bg-white px-4 py-3 font-bold text-ink hover:bg-slate-50 dark:border-slate-600 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800">
                <span class="flex items-center gap-2">
                    <x-ui.icon name="building" size="sm" />
                    {{ __('Stations') }}
                    <span class="text-sm font-normal text-slate-600 dark:text-slate-400">({{ $activeStations }})</span>
                </span>
            </a>
        @endcan
        @canany(['cooldown.manage', 'access.audit-reports'])
            <a href="{{ route('staff.cooldown.show') }}" class="rounded-md border-2 border-slate-400 bg-white px-4 py-3 font-bold text-ink hover:bg-slate-50 dark:border-slate-600 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800">
                <span class="flex items-center gap-2">
                    <x-ui.icon name="clock" size="sm" />
                    {{ __('Cooldown') }}
                </span>
            </a>
        @endcanany
        @can('access.audit-reports')
            <a href="{{ route('staff.reports.index') }}" class="rounded-md border-2 border-slate-400 bg-white px-4 py-3 font-bold text-ink hover:bg-slate-50 dark:border-slate-600 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800">
                <span class="flex items-center gap-2">
                    <x-ui.icon name="chart" size="sm" />
                    {{ __('Reports') }}
                </span>
            </a>
        @endcan
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        @can('vehicles.review')
            <x-ui.card :padding="false">
                <div class="flex items-center justify-between border-b-2 border-slate-300 px-4 py-3 dark:border-slate-600">
                    <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Pending registrations') }}</h2>
                    <a href="{{ route('staff.vehicles.index') }}" class="font-bold text-brand underline dark:text-brand-dark">{{ __('View all') }}</a>
                </div>
                @if ($pendingVehicles->isEmpty())
                    <div class="p-6">
                        <p class="text-base text-slate-700 dark:text-slate-300">{{ __('No pending vehicles') }}</p>
                    </div>
                @else
                    <x-ui.table class="rounded-none border-0">
                        <x-slot name="head">
                            <x-ui.th>{{ __('Plate') }}</x-ui.th>
                            <x-ui.th>{{ __('Owner') }}</x-ui.th>
                            <x-ui.th align="end">{{ __('When') }}</x-ui.th>
                        </x-slot>
                        @foreach ($pendingVehicles as $vehicle)
                            <tr>
                                <x-ui.td>
                                    <a href="{{ route('staff.vehicles.show', $vehicle) }}" class="font-bold text-brand underline dark:text-brand-dark" dir="ltr">
                                        {{ $vehicle->plate_display }}
                                    </a>
                                </x-ui.td>
                                <x-ui.td>{{ $vehicle->owner?->name }}</x-ui.td>
                                <x-ui.td align="end">{{ $vehicle->created_at->diffForHumans() }}</x-ui.td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                @endif
            </x-ui.card>
        @endcan

        @can('access.audit-reports')
            <x-ui.card :padding="false">
                <div class="flex items-center justify-between border-b-2 border-slate-300 px-4 py-3 dark:border-slate-600">
                    <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Recent fill-ups') }}</h2>
                    <a href="{{ route('staff.reports.fill-ups') }}" class="font-bold text-brand underline dark:text-brand-dark">{{ __('View all') }}</a>
                </div>
                @if ($recentFillUps->isEmpty())
                    <div class="p-6">
                        <p class="text-base text-slate-700 dark:text-slate-300">{{ __('No fill-ups recorded yet.') }}</p>
                    </div>
                @else
                    <x-ui.table class="rounded-none border-0">
                        <x-slot name="head">
                            <x-ui.th>{{ __('Plate') }}</x-ui.th>
                            <x-ui.th>{{ __('Station') }}</x-ui.th>
                            <x-ui.th align="end">{{ __('When') }}</x-ui.th>
                        </x-slot>
                        @foreach ($recentFillUps as $fillUp)
                            <tr>
                                <x-ui.td dir="ltr">{{ $fillUp->vehicle?->plate_display }}</x-ui.td>
                                <x-ui.td>{{ $fillUp->station?->name }}</x-ui.td>
                                <x-ui.td align="end">{{ $fillUp->filled_at?->diffForHumans() }}</x-ui.td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                @endif
            </x-ui.card>
        @else
            @cannot('vehicles.review')
                <x-ui.card class="xl:col-span-2">
                    <p class="text-base text-slate-700 dark:text-slate-300">{{ __('Use the sidebar to open the tools available for your role.') }}</p>
                </x-ui.card>
            @endcannot
        @endcan
    </div>
</x-app-layout>
