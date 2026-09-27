<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Cooldown') }}</h1>
    </x-slot>

    <x-ui.page-header
        :title="__('Fill-up cooldown')"
        :description="__('Rolling wait between fill-ups per vehicle. Changes need two different supervisors and only affect fill-ups after approval.')"
    />

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <x-ui.card>
            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Current cooldown') }}</p>
            <p class="mt-1 text-2xl font-bold text-ink dark:text-ink-inverse">{{ $periodLabel }}</p>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400" dir="ltr">{{ $setting->period_hours }} {{ __('hours') }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Rule') }}</p>
            <p class="mt-1 text-base text-ink dark:text-ink-inverse">{{ __('Exact rolling window from the last fill-up timestamp. Not a calendar-day reset.') }}</p>
        </x-ui.card>
    </div>

    @if ($pending)
        <x-ui.card class="mb-6">
            <x-slot name="header">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Pending change') }}</h2>
                    <x-ui.badge :tone="$pending->status->badgeTone()">{{ $pending->status->label() }}</x-ui.badge>
                </div>
            </x-slot>

            <dl class="grid gap-4 text-base sm:grid-cols-2">
                <div>
                    <dt class="font-bold text-slate-600 dark:text-slate-300">{{ __('From') }}</dt>
                    <dd class="text-lg text-ink dark:text-ink-inverse">{{ \App\Support\Cooldown::periodDaysLabel($pending->old_period_hours) }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-600 dark:text-slate-300">{{ __('To') }}</dt>
                    <dd class="text-lg text-ink dark:text-ink-inverse">{{ \App\Support\Cooldown::periodDaysLabel($pending->new_period_hours) }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-600 dark:text-slate-300">{{ __('Proposed by') }}</dt>
                    <dd class="text-lg text-ink dark:text-ink-inverse">{{ $pending->proposer?->name }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-600 dark:text-slate-300">{{ __('When') }}</dt>
                    <dd class="text-lg text-ink dark:text-ink-inverse">{{ $pending->created_at->diffForHumans() }}</dd>
                </div>
            </dl>

            @if ($pending->notes)
                <p class="mt-4 text-base text-slate-700 dark:text-slate-300">{{ $pending->notes }}</p>
            @endif

                    @if ($canManage)
                @if ($pending->proposed_by === auth()->id())
                    <x-ui.alert type="warning" class="mt-4" :title="__('Waiting on a second person')">
                        {{ __('You proposed this change. Another supervisor must approve or reject it.') }}
                    </x-ui.alert>
                @else
                    <div class="mt-6 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('staff.cooldown.approve', $pending) }}">
                            @csrf
                            <x-ui.confirm-button
                                :message="__('Approve this cooldown change?')"
                                class="gap-2 border-transparent bg-brand text-white hover:bg-brand-hover dark:bg-brand dark:hover:bg-brand-hover"
                            >
                                <x-ui.icon name="check" size="sm" />
                                {{ __('Approve') }}
                            </x-ui.confirm-button>
                        </form>
                        <form method="POST" action="{{ route('staff.cooldown.reject', $pending) }}" class="flex flex-wrap items-end gap-2">
                            @csrf
                            <div>
                                <x-input-label for="reject_notes" :value="__('Notes')" />
                                <x-text-input id="reject_notes" name="notes" type="text" class="mt-2 block w-full min-w-56" />
                            </div>
                            <x-ui.confirm-button :message="__('Reject this cooldown change?')" class="gap-2">
                                <x-ui.icon name="x-mark" size="sm" />
                                {{ __('Reject') }}
                            </x-ui.confirm-button>
                        </form>
                    </div>
                @endif
            @endif
        </x-ui.card>
    @elseif ($canManage)
        <x-ui.card class="mb-6">
            <form method="POST" action="{{ route('staff.cooldown.store') }}" class="space-y-6">
                @csrf

                <x-ui.form-section
                    :title="__('Propose a new cooldown')"
                    :description="__('Enter whole days. A second supervisor must approve before it takes effect.')"
                >
                    <div>
                        <x-input-label for="period_days" :value="__('Cooldown days')" />
                        <x-text-input
                            id="period_days"
                            name="period_days"
                            type="number"
                            min="1"
                            max="30"
                            class="mt-2 block w-full"
                            :value="old('period_days', (int) ($setting->period_hours / 24))"
                            required
                            dir="ltr"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('period_days')" />
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            class="ui-focus mt-2 block w-full rounded-md border-2 border-slate-400 bg-white px-3 py-3 text-base text-ink dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse"
                        >{{ old('notes') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>
                </x-ui.form-section>

                <x-primary-button class="gap-2">
                    <x-ui.icon name="check" size="sm" />
                    {{ __('Submit for second approval') }}
                </x-primary-button>
            </form>
        </x-ui.card>
    @endif

    <x-ui.card :padding="false">
        <div class="border-b-2 border-slate-300 px-4 py-3 dark:border-slate-600">
            <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Change history') }}</h2>
        </div>

        @if ($history->isEmpty())
            <div class="p-6">
                <p class="text-base text-slate-700 dark:text-slate-300">{{ __('No cooldown changes yet.') }}</p>
            </div>
        @else
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

            <div class="p-4">
                {{ $history->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>
