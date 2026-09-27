@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
$styles = match ($type) {
    'success' => 'border-green-700 bg-green-50 text-green-950 dark:border-green-400 dark:bg-green-950 dark:text-green-50',
    'warning' => 'border-amber-600 bg-amber-50 text-amber-950 dark:border-amber-400 dark:bg-amber-950 dark:text-amber-50',
    'error', 'danger' => 'border-red-700 bg-red-50 text-red-950 dark:border-red-400 dark:bg-red-950 dark:text-red-50',
    default => 'border-brand bg-brand-soft text-ink dark:border-brand-dark dark:bg-slate-900 dark:text-ink-inverse',
};

$icon = match ($type) {
    'success' => 'check-circle',
    'warning' => 'warning',
    'error', 'danger' => 'x-circle',
    default => 'info',
};
@endphp

<div
    {{ $attributes->merge(['class' => "rounded-md border-2 border-s-8 p-4 {$styles}"]) }}
    @if ($dismissible) x-data="{ show: true }" x-show="show" @endif
    role="alert"
>
    <div class="flex items-start gap-3">
        <x-ui.icon :name="$icon" class="mt-0.5" />

        <div class="min-w-0 flex-1">
            @if ($title)
                <p class="text-lg font-bold">{{ $title }}</p>
            @endif
            <div class="text-base @if($title) mt-1 @endif">
                {{ $slot }}
            </div>
        </div>

        @if ($dismissible)
            <button
                type="button"
                class="ui-focus rounded-md p-1"
                @click="show = false"
                aria-label="{{ __('Dismiss') }}"
            >
                <x-ui.icon name="x-mark" size="sm" />
            </button>
        @endif
    </div>
</div>
