<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-transparent bg-brand px-5 py-3 text-base font-bold text-white hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-50 dark:bg-brand-dark dark:hover:bg-brand']) }}>
    {{ $slot }}
</button>
