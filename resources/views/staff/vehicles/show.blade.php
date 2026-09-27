<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse" dir="ltr">{{ $vehicle->plate_display }}</h1>
    </x-slot>

    <div class="mx-auto max-w-4xl space-y-6">
        <x-ui.card>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Plate') }}</p>
                    <p class="text-2xl font-bold text-ink dark:text-ink-inverse" dir="ltr">{{ $vehicle->plate_display }}</p>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Status') }}</p>
                    <div class="mt-1">
                        <x-ui.badge :tone="$vehicle->status->badgeTone()">{{ $vehicle->status->label() }}</x-ui.badge>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Owner') }}</p>
                    <p class="text-lg font-bold text-ink dark:text-ink-inverse">{{ $vehicle->owner?->name }}</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300" dir="ltr">{{ $vehicle->owner?->national_id }}</p>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Vehicle type') }}</p>
                    <p class="text-lg font-bold text-ink dark:text-ink-inverse">{{ $vehicle->type->label() }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Vehicle photos') }}</h2>
            </x-slot>

            <div class="grid gap-4 sm:grid-cols-3">
                @foreach ($vehicle->images as $image)
                    <div class="space-y-2">
                        <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ $image->type->label() }}</p>
                        <a href="{{ route('vehicle.images.show', $image) }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-md border-2 border-slate-400 dark:border-slate-600">
                            <img src="{{ route('vehicle.images.show', $image) }}" alt="{{ $image->type->label() }}" class="h-48 w-full object-cover">
                        </a>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        @can('review', $vehicle)
            <x-ui.card>
                <x-slot name="header">
                    <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Decision') }}</h2>
                </x-slot>

                <form method="POST" action="{{ route('staff.vehicles.review', $vehicle) }}" class="space-y-4">
                    @csrf

                    <div class="flex flex-wrap gap-3">
                        <label class="inline-flex min-h-12 items-center gap-2 rounded-md border-2 border-slate-400 px-4 py-2 dark:border-slate-500">
                            <input type="radio" name="decision" value="approved" class="text-brand focus:ring-brand" @checked(old('decision', 'approved') === 'approved') required>
                            <span class="font-bold">{{ __('Approve') }}</span>
                        </label>
                        <label class="inline-flex min-h-12 items-center gap-2 rounded-md border-2 border-slate-400 px-4 py-2 dark:border-slate-500">
                            <input type="radio" name="decision" value="rejected" class="text-brand focus:ring-brand" @checked(old('decision') === 'rejected') required>
                            <span class="font-bold">{{ __('Reject') }}</span>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('decision')" />

                    <div>
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            class="ui-focus mt-2 block w-full rounded-md border-2 border-slate-400 bg-white px-3 py-3 text-base text-ink dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse"
                            placeholder="{{ __('Required when rejecting.') }}"
                        >{{ old('notes') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>

                    <x-ui.confirm-button :message="__('Confirm this review decision?')" class="gap-2">
                        <x-ui.icon name="check" size="sm" />
                        {{ __('Save decision') }}
                    </x-ui.confirm-button>
                </form>
            </x-ui.card>
        @endcan

        @if ($vehicle->reviewLogs->isNotEmpty())
            <x-ui.card>
                <x-slot name="header">
                    <h2 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Review history') }}</h2>
                </x-slot>

                <ul class="space-y-3">
                    @foreach ($vehicle->reviewLogs as $log)
                        <li class="rounded-md border-2 border-slate-300 p-3 dark:border-slate-600">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <x-ui.badge :tone="$log->decision === \App\Enums\ReviewDecision::Approved ? 'approved' : 'rejected'">
                                    {{ $log->decision->label() }}
                                </x-ui.badge>
                                <span class="text-sm text-slate-600 dark:text-slate-300">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <p class="mt-2 text-base text-ink dark:text-ink-inverse">{{ $log->reviewer?->name }}</p>
                            @if ($log->notes)
                                <p class="mt-1 text-base text-slate-700 dark:text-slate-300">{{ $log->notes }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-ui.card>
        @endif
    </div>
</x-app-layout>
