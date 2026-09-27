<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse" dir="ltr">{{ $vehicle->plate_display }}</h1>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <x-ui.card>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ __('Plate') }}</p>
                    <p class="text-3xl font-bold text-ink dark:text-ink-inverse" dir="ltr">{{ $vehicle->plate_display }}</p>
                    <p class="mt-2 text-base text-slate-700 dark:text-slate-300">{{ $vehicle->type->label() }}</p>
                </div>
                <x-ui.badge :tone="$vehicle->status->badgeTone()">{{ $vehicle->status->label() }}</x-ui.badge>
            </div>

            @if ($vehicle->status === \App\Enums\VehicleStatus::Rejected && $vehicle->rejection_reason)
                <div class="mt-4">
                    <x-ui.alert type="error" :title="__('Rejection reason')">
                        {{ $vehicle->rejection_reason }}
                    </x-ui.alert>
                </div>
            @endif
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
                            <img src="{{ route('vehicle.images.show', $image) }}" alt="{{ $image->type->label() }}" class="h-40 w-full object-cover">
                        </a>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

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

        <a
            href="{{ route('citizen.vehicles.index') }}"
            class="ui-focus inline-flex min-h-12 items-center justify-center gap-2 rounded-md border-2 border-slate-400 px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800"
        >
            {{ __('Back') }}
        </a>
    </div>
</x-app-layout>
