<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#1d4ed8">

        <title>{{ $title ?? config('app.name', 'نظام توزيع الوقود') }}</title>

        @include('partials.theme-init')
        @include('partials.fonts')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans" x-data="{ sidebarOpen: false }">
        <div class="min-h-full lg:flex">
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-ink/60 lg:hidden"
                @click="sidebarOpen = false"
                style="display: none;"
            ></div>

            <aside
                :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
                class="fixed inset-y-0 start-0 z-50 flex w-[min(20rem,88vw)] flex-col border-e-2 border-slate-300 bg-white transition-transform duration-200 dark:border-slate-600 dark:bg-surface-dark-muted lg:static lg:w-72 lg:translate-x-0"
                @click="if ($event.target.closest('a')) sidebarOpen = false"
            >
                <div class="safe-pt flex h-16 items-center gap-3 border-b-2 border-slate-300 px-4 dark:border-slate-600 sm:px-5">
                    <a href="{{ route(Auth::user()->homeRouteName()) }}" class="flex min-w-0 items-center gap-3 font-bold text-ink dark:text-ink-inverse">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-brand text-lg font-bold text-white">و</span>
                        <span class="truncate text-base leading-tight sm:text-lg">
                            {{ config('app.name', 'نظام توزيع الوقود') }}
                        </span>
                    </a>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto p-3 touch-scroll">
                    @can('access.staff-panel')
                        <x-ui.sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                            {{ __('Dashboard') }}
                        </x-ui.sidebar-link>
                    @else
                        <x-ui.sidebar-link :href="route('citizen.home')" :active="request()->routeIs('citizen.home')" icon="home">
                            {{ __('Home') }}
                        </x-ui.sidebar-link>
                        <x-ui.sidebar-link :href="route('citizen.vehicles.index')" :active="request()->routeIs('citizen.vehicles.*')" icon="truck">
                            {{ __('My vehicles') }}
                        </x-ui.sidebar-link>
                        <x-ui.sidebar-link :href="route('citizen.stations.index')" :active="request()->routeIs('citizen.stations.*')" icon="building">
                            {{ __('Stations') }}
                        </x-ui.sidebar-link>
                    @endcan

                    @can('access.station-desk')
                        <x-ui.sidebar-link :href="route('station.desk')" :active="request()->routeIs('station.*')" icon="building">
                            {{ __('Station desk') }}
                        </x-ui.sidebar-link>
                    @endcan

                    @can('stations.manage')
                        <x-ui.sidebar-link :href="route('staff.stations.index')" :active="request()->routeIs('staff.stations.*')" icon="building">
                            {{ __('Stations') }}
                        </x-ui.sidebar-link>
                    @endcan

                    @can('vehicles.review')
                        <x-ui.sidebar-link :href="route('staff.vehicles.index')" :active="request()->routeIs('staff.vehicles.*')" icon="clipboard">
                            {{ __('Registrations') }}
                        </x-ui.sidebar-link>
                    @endcan

                    @canany(['cooldown.manage', 'access.audit-reports'])
                        <x-ui.sidebar-link :href="route('staff.cooldown.show')" :active="request()->routeIs('staff.cooldown.*')" icon="clock">
                            {{ __('Cooldown') }}
                        </x-ui.sidebar-link>
                    @endcanany

                    @can('access.audit-reports')
                        <x-ui.sidebar-link :href="route('staff.reports.index')" :active="request()->routeIs('staff.reports.*')" icon="chart">
                            {{ __('Reports') }}
                        </x-ui.sidebar-link>
                    @endcan
                </nav>

                <div class="safe-pb space-y-3 border-t-2 border-slate-300 p-4 dark:border-slate-600">
                    <div>
                        <p class="truncate text-base font-semibold text-ink dark:text-ink-inverse">{{ Auth::user()->name }}</p>
                        <p class="truncate text-sm text-slate-600 dark:text-slate-300" dir="ltr">{{ Auth::user()->email }}</p>
                        @if (Auth::user()->roles->isNotEmpty())
                            <p class="mt-1 text-sm font-bold text-brand dark:text-brand-dark">
                                {{ Auth::user()->roles->map(fn ($role) => \App\Enums\AppRole::tryFrom($role->name)?->label() ?? $role->name)->join(' · ') }}
                            </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 lg:hidden">
                        <a
                            href="{{ route('profile.edit') }}"
                            class="ui-focus inline-flex min-h-12 items-center justify-center gap-1.5 rounded-md border-2 border-slate-400 px-2 text-sm font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800"
                        >
                            <x-ui.icon name="user" size="sm" />
                            {{ __('Profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="ui-focus inline-flex min-h-12 w-full items-center justify-center gap-1.5 rounded-md border-2 border-slate-400 px-2 text-sm font-bold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800"
                            >
                                <x-ui.icon name="logout" size="sm" />
                                {{ __('Log out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="safe-pt sticky top-0 z-30 flex min-h-14 items-center gap-2 border-b-2 border-slate-300 bg-white px-3 dark:border-slate-600 dark:bg-surface-dark-muted sm:min-h-16 sm:gap-3 sm:px-6">
                    <button
                        type="button"
                        class="ui-focus inline-flex min-h-12 min-w-12 items-center justify-center rounded-md border-2 border-slate-400 bg-white text-ink dark:border-slate-500 dark:bg-surface-dark dark:text-ink-inverse lg:hidden"
                        @click="sidebarOpen = true"
                        aria-label="{{ __('Open menu') }}"
                    >
                        <x-ui.icon name="menu" />
                    </button>

                    <div class="min-w-0 flex-1">
                        @isset($header)
                            {{ $header }}
                        @else
                            <h1 class="truncate text-lg font-bold text-ink dark:text-ink-inverse sm:text-xl">{{ $title ?? __('Dashboard') }}</h1>
                        @endisset
                    </div>

                    <div class="flex items-center gap-2">
                        <x-ui.theme-toggle />

                        <a
                            href="{{ route('profile.edit') }}"
                            class="ui-focus hidden min-h-12 items-center gap-2 rounded-md border-2 border-slate-400 px-3 text-base font-semibold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800 lg:inline-flex"
                        >
                            <x-ui.icon name="user" size="sm" />
                            {{ __('Profile') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
                            @csrf
                            <button
                                type="submit"
                                class="ui-focus inline-flex min-h-12 items-center gap-2 rounded-md border-2 border-slate-400 px-3 text-base font-semibold text-ink hover:bg-slate-100 dark:border-slate-500 dark:text-ink-inverse dark:hover:bg-slate-800"
                            >
                                <x-ui.icon name="logout" size="sm" />
                                {{ __('Log out') }}
                            </button>
                        </form>
                    </div>
                </header>

                <main class="safe-pb flex-1 p-3 sm:p-6 lg:p-8">
                    <x-ui.flash />
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
