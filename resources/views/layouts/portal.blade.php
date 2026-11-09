<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}BUSina Staff Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-slate-900">
@php
    $user = auth()->user();
    $nav = [
        ['label' => 'Dashboard', 'route' => 'portal.dashboard', 'icon' => 'dashboard', 'active' => 'portal.dashboard'],
        ['label' => 'Reservations', 'route' => 'portal.reservations.index', 'icon' => 'ticket', 'active' => 'portal.reservations.*', 'count' => $pendingCount ?? 0],
        ['label' => 'Buses', 'route' => 'portal.buses.index', 'icon' => 'bus', 'active' => 'portal.buses.*'],
        ['label' => 'Routes', 'route' => 'portal.routes.index', 'icon' => 'map', 'active' => 'portal.routes.*'],
        ['label' => 'Schedules', 'route' => 'portal.schedules.index', 'icon' => 'calendar', 'active' => 'portal.schedules.*'],
    ];
    if ($user->isAdmin()) {
        $nav[] = ['label' => 'Users', 'route' => 'portal.users.index', 'icon' => 'users', 'active' => 'portal.users.*'];
        $nav[] = ['label' => 'Activity Logs', 'route' => 'portal.activity-logs.index', 'icon' => 'logs', 'active' => 'portal.activity-logs.*'];
    }
    $nav[] = ['label' => 'Profile', 'route' => 'profile.show', 'icon' => 'user', 'active' => 'profile.show'];
@endphp

<x-banner />

<div x-data="sidebar" class="min-h-full">
    {{-- Mobile: dark overlay behind the drawer --}}
    <div x-show="mobileOpen" x-transition.opacity x-cloak class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden" @click="mobileOpen = false"></div>

    {{-- Sidebar: drawer on mobile, collapsible on desktop --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-slate-900 transition-all duration-200 lg:translate-x-0"
           :class="[mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', collapsed ? 'lg:w-20' : 'lg:w-64']"
           aria-label="Portal navigation">
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-white/10 px-4">
            <a href="{{ route('portal.dashboard') }}" class="flex items-center">
                <x-logo light size="sm" ::class="collapsed ? 'lg:hidden' : ''" />
                <x-logo light size="sm" :wordmark="false" class="hidden" ::class="collapsed ? 'lg:inline-flex' : ''" />
            </a>
            <button type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-white/10 hover:text-white lg:hidden" @click="mobileOpen = false" aria-label="Close menu">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto p-3">
            @foreach ($nav as $item)
                @php $active = request()->routeIs($item['active']); @endphp
                <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                   class="nav-item {{ $active ? 'nav-item-active' : '' }}" @if ($active) aria-current="page" @endif
                   :class="collapsed ? 'lg:justify-center lg:px-0' : ''">
                    <span class="relative">
                        <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                        @if (! empty($item['count']))
                            <span class="absolute -right-1.5 -top-1.5 hidden h-2.5 w-2.5 rounded-full bg-amber-400 ring-2 ring-slate-900" :class="collapsed ? 'lg:block' : ''"></span>
                        @endif
                    </span>
                    <span class="flex-1 truncate" :class="collapsed ? 'lg:hidden' : ''">{{ $item['label'] }}</span>
                    @if (! empty($item['count']))
                        <span class="rounded-full bg-amber-400 px-2 py-0.5 text-xs font-bold text-slate-900" :class="collapsed ? 'lg:hidden' : ''">{{ $item['count'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="border-t border-white/10 p-3">
            <a href="{{ route('trips.index') }}" class="nav-item" title="View public site" :class="collapsed ? 'lg:justify-center lg:px-0' : ''">
                <x-icon name="home" class="h-5 w-5 shrink-0" />
                <span :class="collapsed ? 'lg:hidden' : ''">Public site</span>
            </a>
            <button type="button" @click="toggle()" class="nav-item hidden w-full lg:flex" :class="collapsed ? 'lg:justify-center lg:px-0' : ''"
                    :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <x-icon name="chevrons-left" class="h-5 w-5 shrink-0 transition-transform" ::class="collapsed ? 'rotate-180' : ''" />
                <span :class="collapsed ? 'lg:hidden' : ''">Collapse</span>
            </button>
        </div>
    </aside>

    {{-- Main column --}}
    <div class="transition-all duration-200" :class="collapsed ? 'lg:pl-20' : 'lg:pl-64'">
        <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
            <button type="button" class="-ml-1 rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" @click="mobileOpen = true" aria-label="Open menu">
                <x-icon name="menu" class="h-6 w-6" />
            </button>
            <div class="min-w-0 flex-1 truncate text-sm text-slate-500">
                @isset($header)
                    {{ $header }}
                @else
                    <span class="hidden sm:inline">{{ now()->format('l, F j, Y') }}</span>
                @endisset
            </div>

            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button type="button" class="flex items-center gap-3 rounded-xl px-2 py-1.5 text-left hover:bg-slate-100">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                        </span>
                        <span class="hidden sm:block">
                            <span class="block text-sm font-semibold text-slate-900">{{ $user->name }}</span>
                            <span class="block text-xs text-slate-500">{{ $user::ROLES[$user->role] ?? $user->role }}</span>
                        </span>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link href="{{ route('profile.show') }}">Profile</x-dropdown-link>
                    <div class="border-t border-slate-100"></div>
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">Log out</x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </header>

        <main class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
            <x-flash />
            {{ $slot }}
        </main>
    </div>
</div>

@stack('modals')
@livewireScripts
</body>
</html>
