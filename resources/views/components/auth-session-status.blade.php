@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-base font-bold text-green-800 dark:text-green-300']) }}>
        {{ $status }}
    </div>
@endif
