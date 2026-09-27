@props([
    'message' => __('Are you sure?'),
    'as' => 'button',
])

@if ($as === 'button')
    <button
        type="submit"
        x-data="confirmAction(@js($message))"
        @click="ask($event)"
        {{ $attributes->merge(['class' => 'ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-transparent bg-red-700 px-5 py-3 text-base font-bold text-white hover:bg-red-800 dark:bg-red-600 dark:hover:bg-red-500']) }}
    >
        {{ $slot }}
    </button>
@else
    <a
        x-data="confirmAction(@js($message))"
        @click="ask($event)"
        {{ $attributes->merge(['class' => 'ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-transparent bg-red-700 px-5 py-3 text-base font-bold text-white hover:bg-red-800 dark:bg-red-600 dark:hover:bg-red-500']) }}
    >
        {{ $slot }}
    </a>
@endif
