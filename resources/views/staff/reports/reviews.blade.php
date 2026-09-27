<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Review audit') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Vehicle review log')"
        :description="__('Immutable record of every approve and reject decision.')"
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
        <form method="GET" action="{{ route('staff.reports.reviews') }}" class="grid gap-4 sm:grid-cols-4">
            <div>
                <x-input-label for="decision" :value="__('Decision')" />
                <select id="decision" name="decision" class="ui-focus mt-2 block w-full rounded-md border-2 border-slate-400 bg-white px-3 py-3 text-base dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse">
                    <option value="">{{ __('All decisions') }}</option>
                    @foreach ($decisions as $decision)
                        <option value="{{ $decision->value }}" @selected($filters['decision'] === $decision->value)>{{ $decision->label() }}</option>
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

    @if ($logs->isEmpty())
        <x-ui.empty-state icon="clipboard" :title="__('No review decisions yet')" :description="__('Approvals and rejections will appear here.')" />
    @else
        <x-ui.card :padding="false">
            <x-ui.table class="rounded-none border-0">
                <x-slot name="head">
                    <x-ui.th>{{ __('Plate') }}</x-ui.th>
                    <x-ui.th>{{ __('Owner') }}</x-ui.th>
                    <x-ui.th>{{ __('Decision') }}</x-ui.th>
                    <x-ui.th>{{ __('Reviewer') }}</x-ui.th>
                    <x-ui.th align="end">{{ __('When') }}</x-ui.th>
                </x-slot>
                @foreach ($logs as $log)
                    <tr>
                        <x-ui.td dir="ltr">{{ $log->vehicle?->plate_display }}</x-ui.td>
                        <x-ui.td>{{ $log->vehicle?->owner?->name }}</x-ui.td>
                        <x-ui.td>
                            <x-ui.badge :tone="$log->decision === \App\Enums\ReviewDecision::Approved ? 'approved' : 'rejected'">
                                {{ $log->decision->label() }}
                            </x-ui.badge>
                        </x-ui.td>
                        <x-ui.td>{{ $log->reviewer?->name }}</x-ui.td>
                        <x-ui.td align="end" dir="ltr">{{ $log->created_at?->format('Y-m-d H:i') }}</x-ui.td>
                    </tr>
                @endforeach
            </x-ui.table>
        </x-ui.card>
        <div class="mt-4">{{ $logs->links() }}</div>
    @endif
</x-app-layout>
