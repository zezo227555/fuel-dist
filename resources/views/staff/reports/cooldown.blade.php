<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Cooldown changes') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Cooldown change audit')"
        :description="__('Full history of proposed and decided cooldown changes.')"
    >
        <x-slot name="actions">
            <a
                href="{{ route('staff.reports.index') }}"
                class="ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-slate-400 px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800"
            >
                {{ __('Back') }}
            </a>
            @can('cooldown.manage')
                <a
                    href="{{ route('staff.cooldown.show') }}"
                    class="ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-transparent bg-brand px-5 py-3 text-base font-bold text-white hover:bg-brand-hover"
                >
                    {{ __('Cooldown settings') }}
                </a>
            @endcan
        </x-slot>
    </x-ui.page-header>

    @if ($history->isEmpty())
        <x-ui.empty-state icon="clock" :title="__('No cooldown changes yet.')" />
    @else
        <x-ui.card :padding="false">
            <x-ui.table class="rounded-none border-0">
                <x-slot name="head">
                    <x-ui.th>{{ __('From') }}</x-ui.th>
                    <x-ui.th>{{ __('To') }}</x-ui.th>
                    <x-ui.th>{{ __('Decision') }}</x-ui.th>
                    <x-ui.th>{{ __('By') }}</x-ui.th>
                    <x-ui.th align="end">{{ __('When') }}</x-ui.th>
                </x-slot>
                @foreach ($history as $item)
                    <tr>
                        <x-ui.td>{{ \App\Support\Cooldown::periodDaysLabel($item->old_period_hours) }}</x-ui.td>
                        <x-ui.td>{{ \App\Support\Cooldown::periodDaysLabel($item->new_period_hours) }}</x-ui.td>
                        <x-ui.td>
                            <x-ui.badge :tone="$item->status->badgeTone()">{{ $item->status->label() }}</x-ui.badge>
                        </x-ui.td>
                        <x-ui.td>
                            <span class="block">{{ $item->proposer?->name }}</span>
                            @if ($item->reviewer)
                                <span class="text-sm text-slate-600 dark:text-slate-400">→ {{ $item->reviewer->name }}</span>
                            @endif
                        </x-ui.td>
                        <x-ui.td align="end">{{ ($item->reviewed_at ?? $item->created_at)->diffForHumans() }}</x-ui.td>
                    </tr>
                @endforeach
            </x-ui.table>
        </x-ui.card>
        <div class="mt-4">{{ $history->links() }}</div>
    @endif
</x-app-layout>
