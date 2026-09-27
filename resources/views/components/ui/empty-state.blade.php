@props([
    'title' => __('Nothing here yet'),
    'description' => null,
    'icon' => 'inbox',
])

<div {{ $attributes->merge(['class' => 'rounded-md border-2 border-dashed border-slate-400 bg-white px-6 py-12 text-center dark:border-slate-600 dark:bg-surface-dark-muted']) }}>
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center overflow-hidden rounded-full border-2 border-slate-300 text-slate-500 dark:border-slate-600 dark:text-slate-300">
        <x-ui.icon :name="$icon" size="md" />
    </div>
    <p class="text-xl font-bold text-ink dark:text-ink-inverse">{{ $title }}</p>
    @if ($description)
        <p class="mx-auto mt-2 max-w-md text-base text-slate-700 dark:text-slate-300">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-6 flex justify-center">
            {{ $action }}
        </div>
    @endisset
</div>
