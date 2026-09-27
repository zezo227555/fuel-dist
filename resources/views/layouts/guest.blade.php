<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#1d4ed8">

        <title>{{ config('app.name', 'نظام توزيع الوقود') }}</title>

        @include('partials.theme-init')
        @include('partials.fonts')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased dark:text-ink-inverse">
        <div class="safe-pt absolute end-3 top-3 z-10 sm:end-4 sm:top-4">
            <x-ui.theme-toggle />
        </div>

        <div class="flex min-h-screen flex-col items-center bg-surface-muted px-3 pt-6 dark:bg-surface-dark sm:justify-center sm:px-0 sm:pt-0">
            <div class="px-2 text-center">
                <a href="/" class="inline-flex max-w-full items-center justify-center gap-3 font-bold text-ink dark:text-ink-inverse">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-brand text-xl font-bold text-white">و</span>
                    <span class="text-lg sm:text-xl">{{ config('app.name', 'نظام توزيع الوقود') }}</span>
                </a>
            </div>

            <div class="safe-pb mt-6 w-full overflow-hidden border-2 border-slate-400 bg-white px-4 py-5 shadow-sm dark:border-slate-600 dark:bg-surface-dark-muted sm:max-w-xl sm:rounded-lg sm:px-6">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
