@props([
    'station' => null,
    'attendants',
    'selectedAttendantIds' => [],
])

@php
    $selectedAttendantIds = old('attendant_ids', $selectedAttendantIds);
@endphp

<div>
    <x-input-label for="name" :value="__('Station name')" />
    <x-text-input id="name" name="name" type="text" class="mt-2 block w-full" :value="old('name', $station?->name)" required autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>

<div>
    <x-input-label for="address" :value="__('Address')" />
    <x-text-input id="address" name="address" type="text" class="mt-2 block w-full" :value="old('address', $station?->address)" />
    <x-input-error class="mt-2" :messages="$errors->get('address')" />
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="latitude" :value="__('Latitude')" />
        <x-text-input id="latitude" name="latitude" type="text" class="mt-2 block w-full" :value="old('latitude', $station?->latitude)" dir="ltr" inputmode="decimal" />
        <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
    </div>
    <div>
        <x-input-label for="longitude" :value="__('Longitude')" />
        <x-text-input id="longitude" name="longitude" type="text" class="mt-2 block w-full" :value="old('longitude', $station?->longitude)" dir="ltr" inputmode="decimal" />
        <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
    </div>
</div>

<div>
    <x-input-label for="map_url" :value="__('Map link')" />
    <x-text-input id="map_url" name="map_url" type="url" class="mt-2 block w-full" :value="old('map_url', $station?->map_url)" dir="ltr" placeholder="https://" />
    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ __('Optional. If empty, coordinates are used for the map link.') }}</p>
    <x-input-error class="mt-2" :messages="$errors->get('map_url')" />
</div>

<div>
    <label class="inline-flex min-h-12 cursor-pointer items-center gap-3">
        <input
            type="checkbox"
            name="is_active"
            value="1"
            class="h-5 w-5 rounded border-2 border-slate-400 text-brand focus:ring-brand dark:border-slate-500 dark:bg-surface-dark"
            @checked((bool) old('is_active', $station?->is_active ?? true))
        >
        <span class="text-base font-bold text-ink dark:text-ink-inverse">{{ __('Active (visible to citizens)') }}</span>
    </label>
    <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
</div>

<div>
    <p class="text-base font-bold text-ink dark:text-ink-inverse">{{ __('Attendants') }}</p>
    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ __('Assign station desk staff. An attendant only works at stations they are assigned to.') }}</p>

    @if ($attendants->isEmpty())
        <p class="mt-3 text-base text-slate-700 dark:text-slate-300">{{ __('No attendant accounts yet. Create staff users with the attendant role first.') }}</p>
    @else
        <div class="mt-3 space-y-2 rounded-md border-2 border-slate-300 p-3 dark:border-slate-600">
            @foreach ($attendants as $attendant)
                <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-md px-2 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <input
                        type="checkbox"
                        name="attendant_ids[]"
                        value="{{ $attendant->id }}"
                        class="h-5 w-5 rounded border-2 border-slate-400 text-brand focus:ring-brand dark:border-slate-500 dark:bg-surface-dark"
                        @checked(in_array($attendant->id, $selectedAttendantIds, true))
                    >
                    <span class="text-base font-bold text-ink dark:text-ink-inverse">{{ $attendant->name }}</span>
                    <span class="text-sm text-slate-600 dark:text-slate-400" dir="ltr">{{ $attendant->email }}</span>
                </label>
            @endforeach
        </div>
    @endif
    <x-input-error class="mt-2" :messages="$errors->get('attendant_ids')" />
</div>
