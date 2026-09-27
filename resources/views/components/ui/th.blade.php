@props([
    'align' => 'start',
])

@php
$alignClass = match ($align) {
    'center' => 'text-center',
    'end' => 'text-end',
    default => 'text-start',
};
@endphp

<th {{ $attributes->merge(['class' => "px-4 py-3 text-sm font-bold text-ink dark:text-ink-inverse {$alignClass}"]) }}>
    {{ $slot }}
</th>
