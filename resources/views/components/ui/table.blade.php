@props([])

<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-md border-2 border-slate-400 touch-scroll dark:border-slate-600']) }}>
    <table class="min-w-full divide-y-2 divide-slate-300 dark:divide-slate-600">
        @isset($head)
            <thead class="bg-slate-200 dark:bg-slate-800">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
        @endisset

        <tbody class="divide-y-2 divide-slate-200 bg-white dark:divide-slate-700 dark:bg-surface-dark-muted">
            {{ $slot }}
        </tbody>
    </table>
</div>
