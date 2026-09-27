@props([
    'tone' => 'neutral',
    'icon' => null,
])

@php
$styles = match ($tone) {
    'success', 'approved', 'eligible' => 'border-green-800 bg-green-100 text-green-950 dark:border-green-300 dark:bg-green-900 dark:text-green-50',
    'warning', 'pending' => 'border-amber-700 bg-amber-100 text-amber-950 dark:border-amber-300 dark:bg-amber-900 dark:text-amber-50',
    'danger', 'rejected', 'blocked' => 'border-red-800 bg-red-100 text-red-950 dark:border-red-300 dark:bg-red-900 dark:text-red-50',
    'info' => 'border-blue-800 bg-blue-100 text-blue-950 dark:border-blue-300 dark:bg-blue-900 dark:text-blue-50',
    default => 'border-slate-600 bg-slate-100 text-slate-950 dark:border-slate-400 dark:bg-slate-800 dark:text-slate-50',
};

$resolvedIcon = $icon ?? match ($tone) {
    'success', 'approved', 'eligible' => 'check-circle',
    'warning', 'pending' => 'clock',
    'danger', 'rejected', 'blocked' => 'ban',
    'info' => 'info',
    default => null,
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded border-2 px-2.5 py-1 text-sm font-bold {$styles}"]) }}>
    @if ($resolvedIcon)
        <x-ui.icon :name="$resolvedIcon" size="xs" />
    @endif
    {{ $slot }}
</span>
