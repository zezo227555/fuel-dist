@props([
    'name',
    'label',
    'hint' => null,
    'accept' => 'image/*',
    'required' => false,
])

<div {{ $attributes->only('class')->merge(['class' => 'space-y-2']) }}>
    <label for="{{ $name }}" class="block text-base font-bold text-ink dark:text-ink-inverse">
        {{ $label }}
        @if ($required)
            <span class="text-red-700 dark:text-red-400">*</span>
        @endif
    </label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="file"
        accept="{{ $accept }}"
        @required($required)
        {{ $attributes->except('class')->merge([
            'class' => 'block w-full cursor-pointer rounded-md border-2 border-slate-400 bg-white px-3 py-3 text-base text-ink file:me-4 file:rounded file:border-0 file:bg-slate-200 file:px-4 file:py-2 file:text-base file:font-bold file:text-ink hover:border-slate-500 dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse dark:file:bg-slate-700 dark:file:text-ink-inverse',
        ]) }}
    >

    @if ($hint)
        <p class="text-sm text-slate-600 dark:text-slate-300">{{ $hint }}</p>
    @endif
</div>
