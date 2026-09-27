@props([
    'title',
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'space-y-4']) }}>
    <div>
        <h3 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ $title }}</h3>
        @if ($description)
            <p class="mt-1 text-base text-slate-700 dark:text-slate-300">{{ $description }}</p>
        @endif
    </div>

    <div class="space-y-4">
        {{ $slot }}
    </div>
</section>
