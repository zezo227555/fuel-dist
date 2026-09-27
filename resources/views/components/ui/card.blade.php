@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-md border-2 border-slate-400 bg-white dark:border-slate-600 dark:bg-surface-dark-muted']) }}>
    @isset($header)
        <div class="border-b-2 border-slate-300 px-4 py-3 dark:border-slate-600 sm:px-5">
            {{ $header }}
        </div>
    @endisset

    <div @class(['px-4 py-4 sm:px-5 sm:py-5' => $padding])>
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t-2 border-slate-300 px-4 py-3 dark:border-slate-600 sm:px-5">
            {{ $footer }}
        </div>
    @endisset
</div>
