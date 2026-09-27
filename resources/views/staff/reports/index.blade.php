<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Reports') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Audit & reports')"
        :description="__('Read-only views of fill-ups, denials, reviews, and cooldown changes.')"
    />

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat :label="__('Fills today')" :value="(string) $fillsToday" :hint="__('All stations')" icon="truck" />
        <x-ui.stat :label="__('Denied attempts')" :value="(string) $denialsToday" :hint="__('Today')" icon="ban" />
        <x-ui.stat :label="__('Pending reviews')" :value="(string) $pendingReviews" :hint="__('Waiting on documents')" icon="clipboard" />
        <x-ui.stat :label="__('Active stations')" :value="(string) $stationsCount" icon="building" />
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <a href="{{ route('staff.reports.fill-ups') }}" class="block rounded-md border-2 border-slate-400 bg-white p-5 hover:bg-slate-50 dark:border-slate-600 dark:bg-surface-dark dark:hover:bg-slate-800">
            <div class="flex items-center gap-2 text-brand dark:text-brand-dark">
                <x-ui.icon name="truck" size="sm" />
                <h2 class="text-lg font-bold">{{ __('Fill-ups') }}</h2>
            </div>
            <p class="mt-2 text-base text-slate-700 dark:text-slate-300">{{ __('Per-station fill-up counts and timestamps.') }}</p>
        </a>
        <a href="{{ route('staff.reports.vehicle-history') }}" class="block rounded-md border-2 border-slate-400 bg-white p-5 hover:bg-slate-50 dark:border-slate-600 dark:bg-surface-dark dark:hover:bg-slate-800">
            <div class="flex items-center gap-2 text-brand dark:text-brand-dark">
                <x-ui.icon name="search" size="sm" />
                <h2 class="text-lg font-bold">{{ __('Vehicle history') }}</h2>
            </div>
            <p class="mt-2 text-base text-slate-700 dark:text-slate-300">{{ __('Look up a plate for fill-ups and denied attempts.') }}</p>
        </a>
        <a href="{{ route('staff.reports.denials') }}" class="block rounded-md border-2 border-slate-400 bg-white p-5 hover:bg-slate-50 dark:border-slate-600 dark:bg-surface-dark dark:hover:bg-slate-800">
            <div class="flex items-center gap-2 text-brand dark:text-brand-dark">
                <x-ui.icon name="ban" size="sm" />
                <h2 class="text-lg font-bold">{{ __('Denied attempts') }}</h2>
            </div>
            <p class="mt-2 text-base text-slate-700 dark:text-slate-300">{{ __('Blocked lookups and confirm attempts.') }}</p>
        </a>
        <a href="{{ route('staff.reports.reviews') }}" class="block rounded-md border-2 border-slate-400 bg-white p-5 hover:bg-slate-50 dark:border-slate-600 dark:bg-surface-dark dark:hover:bg-slate-800">
            <div class="flex items-center gap-2 text-brand dark:text-brand-dark">
                <x-ui.icon name="clipboard" size="sm" />
                <h2 class="text-lg font-bold">{{ __('Review audit') }}</h2>
            </div>
            <p class="mt-2 text-base text-slate-700 dark:text-slate-300">{{ __('Every vehicle approval and rejection.') }}</p>
        </a>
        <a href="{{ route('staff.reports.cooldown') }}" class="block rounded-md border-2 border-slate-400 bg-white p-5 hover:bg-slate-50 dark:border-slate-600 dark:bg-surface-dark dark:hover:bg-slate-800 sm:col-span-2">
            <div class="flex items-center gap-2 text-brand dark:text-brand-dark">
                <x-ui.icon name="clock" size="sm" />
                <h2 class="text-lg font-bold">{{ __('Cooldown changes') }}</h2>
            </div>
            <p class="mt-2 text-base text-slate-700 dark:text-slate-300">{{ __('Two-person cooldown proposals and decisions.') }}</p>
        </a>
    </div>

    <x-ui.card :padding="false">
        <div class="border-b-2 border-slate-300 px-4 py-3 dark:border-slate-600">
            <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Top stations by fill-ups') }}</h2>
        </div>
        @if ($stationTotals->isEmpty() || $stationTotals->every(fn ($s) => $s->fill_ups_count === 0))
            <div class="p-6">
                <p class="text-base text-slate-700 dark:text-slate-300">{{ __('No fill-ups recorded yet.') }}</p>
            </div>
        @else
            <x-ui.table class="rounded-none border-0">
                <x-slot name="head">
                    <x-ui.th>{{ __('Station') }}</x-ui.th>
                    <x-ui.th align="end">{{ __('Fill-ups') }}</x-ui.th>
                </x-slot>
                @foreach ($stationTotals->filter(fn ($s) => $s->fill_ups_count > 0) as $station)
                    <tr>
                        <x-ui.td>{{ $station->name }}</x-ui.td>
                        <x-ui.td align="end">{{ $station->fill_ups_count }}</x-ui.td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif
    </x-ui.card>
</x-app-layout>
