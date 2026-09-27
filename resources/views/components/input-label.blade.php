@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-base font-bold text-ink dark:text-ink-inverse']) }}>
    {{ $value ?? $slot }}
</label>
