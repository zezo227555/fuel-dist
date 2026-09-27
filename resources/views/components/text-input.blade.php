@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'ui-focus block w-full rounded-md border-2 border-slate-400 bg-white px-3 py-3 text-base text-ink shadow-none placeholder:text-slate-500 focus:border-brand focus:ring-0 disabled:bg-slate-100 disabled:text-slate-500 dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse dark:placeholder:text-slate-400 dark:disabled:bg-slate-800',
    ]) }}
>
