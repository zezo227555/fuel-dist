<button {{ $attributes->merge(['type' => 'button', 'class' => 'ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-slate-400 bg-white px-5 py-3 text-base font-bold text-ink hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800']) }}>
    {{ $slot }}
</button>
