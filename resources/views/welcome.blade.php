<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#1d4ed8">
        <meta name="description" content="{{ __('Fair fuel distribution for the citizens of Sebha.') }}">

        <title>{{ config('app.name', 'نظام توزيع الوقود') }}</title>

        @include('partials.theme-init')
        @include('partials.fonts')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full font-sans antialiased">
        <div class="welcome-shell relative min-h-screen overflow-hidden">
            <div class="welcome-sky pointer-events-none absolute inset-0" aria-hidden="true"></div>
            <div class="welcome-grain pointer-events-none absolute inset-0 opacity-40 dark:opacity-20" aria-hidden="true"></div>

            <header class="safe-pt relative z-20 flex items-center justify-between gap-3 px-4 py-4 sm:px-8">
                <p class="truncate text-sm font-bold tracking-wide text-white/90">
                    {{ __('Municipality of Sebha') }}
                </p>
                <div class="flex shrink-0 items-center gap-2">
                    <x-ui.theme-toggle />
                    @auth
                        <a
                            href="{{ route(auth()->user()->homeRouteName()) }}"
                            class="ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-white/40 bg-white/10 px-4 text-base font-bold text-white backdrop-blur hover:bg-white/20"
                        >
                            {{ __('Enter system') }}
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="ui-focus inline-flex min-h-12 items-center justify-center rounded-md border-2 border-white/40 bg-white/10 px-4 text-base font-bold text-white backdrop-blur hover:bg-white/20"
                        >
                            {{ __('Log in') }}
                        </a>
                    @endauth
                </div>
            </header>

            <main class="relative z-10 flex min-h-[calc(100vh-5rem)] flex-col justify-end px-4 pb-10 pt-8 sm:px-8 sm:pb-14 lg:justify-center lg:pb-20">
                <div class="welcome-copy mx-auto w-full max-w-3xl">
                    <p class="welcome-fade text-sm font-bold tracking-wide text-brand-soft sm:text-base">
                        {{ __('Municipality of Sebha') }}
                    </p>

                    <h1 class="welcome-rise mt-3 text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                        {{ config('app.name', 'نظام توزيع الوقود') }}
                    </h1>

                    <p class="welcome-rise-delay mt-4 max-w-xl text-lg text-slate-100 sm:text-xl">
                        {{ __('Fair fuel distribution for the citizens of Sebha.') }}
                    </p>

                    <div class="welcome-rise-delay-2 mt-8 flex w-full flex-col gap-3 sm:max-w-md sm:flex-row">
                        @auth
                            <a
                                href="{{ route(auth()->user()->homeRouteName()) }}"
                                class="ui-focus inline-flex min-h-14 flex-1 items-center justify-center gap-2 rounded-md border-2 border-transparent bg-white px-6 text-lg font-bold text-brand hover:bg-slate-100"
                            >
                                <x-ui.icon name="arrow-left" size="sm" />
                                {{ __('Go to my account') }}
                            </a>
                        @else
                            <a
                                href="{{ route('register') }}"
                                class="ui-focus inline-flex min-h-14 flex-1 items-center justify-center gap-2 rounded-md border-2 border-transparent bg-white px-6 text-lg font-bold text-brand hover:bg-slate-100"
                            >
                                <x-ui.icon name="user" size="sm" />
                                {{ __('Create citizen account') }}
                            </a>
                            <a
                                href="{{ route('login') }}"
                                class="ui-focus inline-flex min-h-14 flex-1 items-center justify-center gap-2 rounded-md border-2 border-white/50 bg-transparent px-6 text-lg font-bold text-white hover:bg-white/10"
                            >
                                {{ __('Staff or citizen login') }}
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="welcome-mark pointer-events-none absolute inset-x-0 bottom-0 top-24 -z-0 opacity-30 sm:opacity-40 lg:inset-y-0 lg:start-auto lg:end-0 lg:w-1/2" aria-hidden="true">
                    <svg class="h-full w-full" viewBox="0 0 480 640" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMax meet">
                        <path d="M0 420 L120 360 L240 400 L360 320 L480 380 L480 640 L0 640 Z" fill="currentColor" class="text-white/20"/>
                        <path d="M0 480 L140 430 L260 470 L380 400 L480 450 L480 640 L0 640 Z" fill="currentColor" class="text-white/15"/>
                        <rect x="300" y="280" width="70" height="160" rx="4" fill="currentColor" class="text-white/35"/>
                        <rect x="318" y="250" width="34" height="40" rx="2" fill="currentColor" class="text-white/45"/>
                        <rect x="280" y="360" width="110" height="18" rx="2" fill="currentColor" class="text-white/50"/>
                        <circle cx="315" cy="430" r="22" stroke="currentColor" stroke-width="8" class="text-white/55" fill="none"/>
                        <circle cx="355" cy="430" r="22" stroke="currentColor" stroke-width="8" class="text-white/55" fill="none"/>
                        <path d="M80 500 V360 H120 V500" stroke="currentColor" stroke-width="10" class="text-white/30"/>
                        <path d="M100 360 V300" stroke="currentColor" stroke-width="8" class="text-white/40"/>
                        <circle cx="100" cy="290" r="14" fill="currentColor" class="text-amber-300/80"/>
                    </svg>
                </div>
            </main>
        </div>

        <section class="border-t-2 border-slate-300 bg-surface-muted px-4 py-14 dark:border-slate-700 dark:bg-surface-dark sm:px-8">
            <div class="mx-auto max-w-3xl">
                <h2 class="text-2xl font-bold text-ink dark:text-ink-inverse sm:text-3xl">{{ __('How it works') }}</h2>
                <p class="mt-2 max-w-2xl text-base text-slate-700 dark:text-slate-300">
                    {{ __('Register once, get your vehicle approved, then fill up when eligible.') }}
                </p>

                <ol class="mt-10 space-y-8">
                    <li class="flex gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md border-2 border-brand bg-brand-soft text-lg font-bold text-brand dark:bg-slate-800 dark:text-brand-dark" dir="ltr">1</span>
                        <div>
                            <h3 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Create your account') }}</h3>
                            <p class="mt-1 text-base text-slate-700 dark:text-slate-300">{{ __('Submit your national ID, passport, and a clear selfie.') }}</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md border-2 border-brand bg-brand-soft text-lg font-bold text-brand dark:bg-slate-800 dark:text-brand-dark" dir="ltr">2</span>
                        <div>
                            <h3 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Register your vehicle') }}</h3>
                            <p class="mt-1 text-base text-slate-700 dark:text-slate-300">{{ __('Upload plate photos. A reviewer approves before you can fill up.') }}</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md border-2 border-brand bg-brand-soft text-lg font-bold text-brand dark:bg-slate-800 dark:text-brand-dark" dir="ltr">3</span>
                        <div>
                            <h3 class="text-lg font-bold text-ink dark:text-ink-inverse">{{ __('Fill at a station') }}</h3>
                            <p class="mt-1 text-base text-slate-700 dark:text-slate-300">{{ __('Visit an assigned station when your cooldown window allows.') }}</p>
                        </div>
                    </li>
                </ol>

                @guest
                    <div class="mt-10">
                        <a
                            href="{{ route('register') }}"
                            class="ui-focus inline-flex min-h-14 items-center justify-center gap-2 rounded-md border-2 border-transparent bg-brand px-6 text-lg font-bold text-white hover:bg-brand-hover"
                        >
                            {{ __('Start registration') }}
                        </a>
                    </div>
                @endguest
            </div>
        </section>

        <footer class="safe-pb border-t-2 border-slate-300 bg-white px-4 py-6 text-center dark:border-slate-700 dark:bg-surface-dark-muted sm:px-8">
            <p class="text-sm font-bold text-slate-600 dark:text-slate-400">
                {{ __('Municipality of Sebha') }} · {{ config('app.name') }}
            </p>
        </footer>
    </body>
</html>
