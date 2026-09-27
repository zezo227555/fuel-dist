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

<td {{ $attributes->merge(['class' => "px-4 py-3 text-base text-ink dark:text-ink-inverse {$alignClass}"]) }}>
    {{ $slot }}
</td>
