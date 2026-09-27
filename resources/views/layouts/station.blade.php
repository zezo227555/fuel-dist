<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#1d4ed8">

        <title>{{ $title ?? __('Station desk') }} — {{ config('app.name', 'نظام توزيع الوقود') }}</title>

        @include('partials.theme-init')
        @include('partials.fonts')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full font-sans">
        <div class="mx-auto flex min-h-full max-w-3xl flex-col">
            <header class="safe-pt sticky top-0 z-30 flex items-center gap-2 border-b-4 border-brand bg-white px-3 py-3 dark:bg-surface-dark-muted sm:gap-3 sm:px-6 sm:py-4">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-bold tracking-wide text-brand dark:text-brand-dark sm:text-sm">{{ config('app.name', 'نظام توزيع الوقود') }}</p>
                    <h1 class="truncate text-xl font-bold text-ink dark:text-ink-inverse sm:text-2xl">{{ $title ?? __('Station desk') }}</h1>
                </div>

                <x-ui.theme-toggle />

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="ui-focus inline-flex min-h-12 items-center gap-2 rounded-md border-2 border-slate-400 px-3 text-sm font-semibold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800 sm:text-base"
                        >
                            <x-ui.icon name="logout" size="sm" />
                            {{ __('Log out') }}
                        </button>
                    </form>
                @endauth
            </header>

            <main @class([
                'flex-1 space-y-4 p-3 sm:space-y-6 sm:p-6',
                'pb-28 sm:pb-6' => isset($stickyActions),
            ])>
                <x-ui.flash />
                {{ $slot }}
            </main>

            @isset($stickyActions)
                <div class="safe-pb fixed inset-x-0 bottom-0 z-40 border-t-2 border-slate-400 bg-white/95 p-3 backdrop-blur dark:border-slate-600 dark:bg-surface-dark-muted/95 sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:backdrop-blur-none dark:sm:bg-transparent">
                    <div class="mx-auto flex max-w-3xl flex-col gap-2 sm:px-6 sm:pb-6">
                        {{ $stickyActions }}
                    </div>
                </div>
            @endisset
        </div>
    </body>
</html>
