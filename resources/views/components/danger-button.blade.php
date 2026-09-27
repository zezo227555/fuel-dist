<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ui-focus inline-flex min-h-touch items-center justify-center rounded-md border-2 border-transparent bg-red-700 px-5 py-3 text-base font-bold text-white hover:bg-red-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-red-600 dark:hover:bg-red-500']) }}>
    {{ $slot }}
</button>
