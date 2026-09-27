@props([
    'href' => '#',
    'active' => false,
    'disabled' => false,
    'icon' => null,
])

@php
$classes = $active
    ? 'bg-brand text-white'
    : 'text-ink hover:bg-slate-200 dark:text-ink-inverse dark:hover:bg-slate-700';

$disabledClasses = 'cursor-not-allowed text-slate-400 dark:text-slate-500';
$base = 'flex min-h-12 items-center gap-3 rounded-md px-3 text-base font-semibold';
@endphp

@if ($disabled)
    <span {{ $attributes->merge(['class' => "{$base} {$disabledClasses}"]) }} aria-disabled="true">
        @if ($icon)
            <x-ui.icon :name="$icon" size="sm" />
        @endif
        <span>{{ $slot }}</span>
    </span>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "ui-focus {$base} {$classes}"]) }}>
        @if ($icon)
            <x-ui.icon :name="$icon" size="sm" />
        @endif
        <span>{{ $slot }}</span>
    </a>
@endif
