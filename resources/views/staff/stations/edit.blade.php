<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Edit station') }}</h1>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <x-ui.card>
            <form method="POST" action="{{ route('staff.stations.update', $station) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <x-ui.form-section :title="__('Station details')">
                    @include('staff.stations._form', [
                        'station' => $station,
                        'attendants' => $attendants,
                        'selectedAttendantIds' => $selectedAttendantIds,
                    ])
                </x-ui.form-section>

                <div class="flex flex-wrap gap-2">
                    <x-primary-button class="gap-2">
                        <x-ui.icon name="check" size="sm" />
                        {{ __('Save changes') }}
                    </x-primary-button>
                    <a
                        href="{{ route('staff.stations.show', $station) }}"
                        class="ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-slate-400 px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800"
                    >
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
