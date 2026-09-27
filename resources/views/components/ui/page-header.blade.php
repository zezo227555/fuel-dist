@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'mb-5 flex flex-col gap-3 sm:mb-6 sm:flex-row sm:items-end sm:justify-between sm:gap-4']) }}>
    <div class="min-w-0">
        <h2 class="text-xl font-bold text-ink dark:text-ink-inverse sm:text-2xl">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1 text-base text-slate-700 dark:text-slate-300">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:flex-wrap sm:items-center">
            {{ $actions }}
        </div>
    @endisset
</div>
