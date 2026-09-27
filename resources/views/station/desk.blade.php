<x-station-layout>
    <x-slot name="title">{{ __('Station desk') }}</x-slot>

    @if ($stations->isEmpty())
        <x-ui.empty-state
            icon="building"
            :title="__('No station assigned')"
            :description="__('Ask a supervisor to assign you to an active station before recording fill-ups.')"
        />
    @else
        <x-ui.card>
            <form method="POST" action="{{ route('station.lookup') }}" class="space-y-4">
                @csrf

                <x-ui.form-section
                    :title="__('Look up vehicle')"
                    :description="__('Type the plate clearly. Large fields for outdoor use.')"
                >
                    @if ($stations->count() > 1)
                        <div>
                            <x-input-label for="station_id" :value="__('Station')" />
                            <select
                                id="station_id"
                                name="station_id"
                                class="ui-focus mt-2 block w-full rounded-md border-2 border-slate-400 bg-white px-3 py-3 text-base font-bold text-ink dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse"
                                required
                            >
                                <option value="">{{ __('Select station') }}</option>
                                @foreach ($stations as $station)
                                    <option value="{{ $station->id }}" @selected((string) $selectedStationId === (string) $station->id)>
                                        {{ $station->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('station_id')" />
                        </div>
                    @else
                        <input type="hidden" name="station_id" value="{{ $stations->first()->id }}">
                        <div>
                            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Station') }}</p>
                            <p class="mt-1 text-lg font-bold text-ink dark:text-ink-inverse">{{ $stations->first()->name }}</p>
                        </div>
                    @endif

                    <div>
                        <x-input-label for="plate_number" :value="__('Plate number')" />
                        <div class="relative mt-2">
                            <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center pe-0 ps-3.5 text-slate-500 dark:text-slate-400">
                                <x-ui.icon name="search" size="sm" />
                            </span>
                            <x-text-input
                                id="plate_number"
                                name="plate_number"
                                class="block w-full ps-11 text-2xl font-bold tracking-wide"
                                type="text"
                                inputmode="text"
                                enterkeyhint="search"
                                placeholder="ABC-123"
                                autocomplete="off"
                                autocapitalize="characters"
                                dir="ltr"
                                :value="$plateDisplay"
                                required
                                autofocus
                            />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('plate_number')" />
                    </div>

                    <x-primary-button class="min-h-14 w-full gap-2 text-lg">
                        <x-ui.icon name="search" />
                        {{ __('Check now') }}
                    </x-primary-button>
                </x-ui.form-section>
            </form>
        </x-ui.card>

        @if ($eligibility)
            <x-ui.card>
                <x-slot name="header">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="flex items-center gap-1.5 text-sm font-bold text-slate-600 dark:text-slate-300">
                                <x-ui.icon name="truck" size="xs" />
                                {{ __('Result') }}
                            </p>
                            <p class="truncate text-3xl font-bold text-ink dark:text-ink-inverse sm:text-4xl" dir="ltr">
                                {{ $eligibility->vehicle?->plate_display ?? $plateDisplay }}
                            </p>
                        </div>
                        @if ($eligibility->eligible)
                            <x-ui.badge tone="eligible">{{ __('Eligible') }}</x-ui.badge>
                        @else
                            <x-ui.badge tone="blocked">{{ $eligibility->denialReason?->label() ?? __('Blocked') }}</x-ui.badge>
                        @endif
                    </div>
                </x-slot>

                <dl class="grid gap-4 text-base sm:grid-cols-2">
                    <div>
                        <dt class="flex items-center gap-1.5 font-bold text-slate-600 dark:text-slate-300">
                            <x-ui.icon name="user" size="xs" />
                            {{ __('Owner') }}
                        </dt>
                        <dd class="text-lg text-ink dark:text-ink-inverse">
                            {{ $eligibility->vehicle?->owner?->name ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="flex items-center gap-1.5 font-bold text-slate-600 dark:text-slate-300">
                            <x-ui.icon name="clock" size="xs" />
                            {{ __('Last fill') }}
                        </dt>
                        <dd class="text-lg text-ink dark:text-ink-inverse">
                            {{ $eligibility->lastFilledAt?->diffForHumans() ?? __('Never') }}
                        </dd>
                    </div>
                    @if ($eligibility->eligibleAt)
                        <div class="sm:col-span-2">
                            <dt class="flex items-center gap-1.5 font-bold text-slate-600 dark:text-slate-300">
                                <x-ui.icon name="clock" size="xs" />
                                {{ __('Next eligible') }}
                            </dt>
                            <dd class="text-lg text-ink dark:text-ink-inverse">
                                {{ $eligibility->eligibleAt->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                                <span class="text-base text-slate-600 dark:text-slate-400">({{ $eligibility->eligibleAt->diffForHumans() }})</span>
                            </dd>
                        </div>
                    @endif
                    <div class="sm:col-span-2">
                        <dt class="font-bold text-slate-600 dark:text-slate-300">{{ __('Current cooldown') }}</dt>
                        <dd class="text-base text-ink dark:text-ink-inverse">{{ $periodLabel ?? \App\Support\Cooldown::periodDaysLabel($eligibility->cooldownHours) }}</dd>
                    </div>
                </dl>
            </x-ui.card>
        @endif
    @endif

    @if ($eligibility?->eligible && $eligibility->vehicle && $selectedStationId)
        <x-slot name="stickyActions">
            <form method="POST" action="{{ route('station.fill') }}">
                @csrf
                <input type="hidden" name="station_id" value="{{ $selectedStationId }}">
                <input type="hidden" name="vehicle_id" value="{{ $eligibility->vehicle->id }}">
                <x-ui.confirm-button
                    :message="__('Confirm fill-up for this vehicle?')"
                    class="min-h-14 w-full gap-2 border-transparent bg-brand text-lg text-white hover:bg-brand-hover dark:bg-brand dark:hover:bg-brand-hover"
                >
                    <x-ui.icon name="check" />
                    {{ __('Confirm fill-up') }}
                </x-ui.confirm-button>
            </form>
        </x-slot>
    @endif
</x-station-layout>
