<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse">{{ __('Add vehicle') }}</h1>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <x-ui.card>
            <form method="POST" action="{{ route('citizen.vehicles.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <x-ui.form-section :title="__('Vehicle details')">
                    <div>
                        <x-input-label for="plate_number" :value="__('Plate number')" />
                        <x-text-input id="plate_number" name="plate_number" type="text" class="mt-2 block w-full text-xl font-bold" :value="old('plate_number')" required dir="ltr" autocomplete="off" />
                        <x-input-error class="mt-2" :messages="$errors->get('plate_number')" />
                    </div>

                    <div>
                        <x-input-label for="type" :value="__('Vehicle type')" />
                        <select
                            id="type"
                            name="type"
                            class="ui-focus mt-2 block w-full rounded-md border-2 border-slate-400 bg-white px-3 py-3 text-base text-ink dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse"
                            required
                        >
                            @foreach (\App\Enums\VehicleType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('type')" />
                    </div>
                </x-ui.form-section>

                <x-ui.form-section :title="__('Vehicle photos')" :description="__('Clear photos help reviewers approve faster.')">
                    <x-ui.file-upload name="front_image" :label="__('Front image')" required />
                    <x-input-error :messages="$errors->get('front_image')" />

                    <x-ui.file-upload name="rear_image" :label="__('Rear image')" required />
                    <x-input-error :messages="$errors->get('rear_image')" />

                    <x-ui.file-upload name="side_image" :label="__('Side image')" required />
                    <x-input-error :messages="$errors->get('side_image')" />
                </x-ui.form-section>

                <div class="flex flex-wrap gap-2">
                    <x-primary-button class="gap-2">
                        <x-ui.icon name="check" size="sm" />
                        {{ __('Submit for review') }}
                    </x-primary-button>
                    <a
                        href="{{ route('citizen.vehicles.index') }}"
                        class="ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-slate-400 px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800"
                    >
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
