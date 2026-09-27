@props([
    'label',
    'value',
    'hint' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-md border-2 border-slate-400 bg-white p-4 dark:border-slate-600 dark:bg-surface-dark-muted']) }}>
    <div class="flex items-start justify-between gap-3">
        <p class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ $label }}</p>
        @if ($icon)
            <span class="rounded-md bg-slate-100 p-1.5 text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                <x-ui.icon :name="$icon" size="sm" />
            </span>
        @endif
    </div>
    <p class="mt-1 text-3xl font-bold tabular-nums text-ink dark:text-ink-inverse">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $hint }}</p>
    @endif
</div>
