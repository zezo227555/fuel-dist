<button
    type="button"
    x-data="themeToggle"
    @click="toggle"
    class="ui-focus inline-flex min-h-12 min-w-12 items-center justify-center rounded-md border-2 border-slate-400 bg-white text-ink hover:bg-slate-100 dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse dark:hover:bg-slate-800"
    :aria-label="dark ? '{{ __('Switch to light mode') }}' : '{{ __('Switch to dark mode') }}'"
>
    <span x-show="dark">
        <x-ui.icon name="sun" />
    </span>
    <span x-show="!dark" style="display: none;">
        <x-ui.icon name="moon" />
    </span>
</button>
