<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? null) ? $title.' · ' : '' }}BUSina - Bus Reservations</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-full flex-col bg-slate-50 font-sans antialiased text-slate-900">
@php
    $user = auth()->user();
    $links = [
        ['label' => 'Home', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Search Trips', 'href' => route('trips.index'), 'active' => request()->routeIs('trips.*')],
        ['label' => 'Routes', 'href' => route('routes.list'), 'active' => request()->routeIs('routes.list')],
    ];
    if ($user?->isCustomer()) {
        array_unshift($links, ['label' => 'Dashboard', 'href' => route('customer.dashboard'), 'active' => request()->routeIs('customer.dashboard')]);
        $links[] = ['label' => 'My Reservations', 'href' => route('customer.reservations.index'), 'active' => request()->routeIs('customer.reservations.*')];
        $links = array_values(array_filter($links, fn ($l) => $l['label'] !== 'Home'));
    }
@endphp

<x-banner />

<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="{{ $user?->isCustomer() ? route('customer.dashboard') : route('home') }}" aria-label="BUSina home">
            <x-logo size="sm" />
        </a>

        <nav class="hidden items-center gap-1 md:flex" aria-label="Main">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}" @if ($link['active']) aria-current="page" @endif
                   class="rounded-lg px-3 py-2 text-sm font-medium {{ $link['active'] ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-2 md:flex">
            @guest
                <a href="{{ route('login') }}" class="btn-ghost">Log in</a>
                <a href="{{ route('register') }}" class="btn-primary">Sign up</a>
            @else
                @if ($user->isStaff())
                    <a href="{{ route('portal.dashboard') }}" class="btn-secondary">Staff portal</a>
                @endif
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="flex items-center gap-2 rounded-xl px-2 py-1.5 hover:bg-slate-100">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                            <span class="text-sm font-semibold text-slate-800">{{ \Illuminate\Support\Str::before($user->name, ' ') }}</span>
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
            @endguest
        </div>

        <button type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" @click="open = !open" :aria-expanded="open" aria-label="Menu">
            <x-icon name="menu" class="h-6 w-6" x-show="!open" />
            <x-icon name="x" class="h-6 w-6" x-show="open" x-cloak />
        </button>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition x-cloak class="border-t border-slate-200 bg-white px-4 pb-4 pt-2 md:hidden">
        @foreach ($links as $link)
            <a href="{{ $link['href'] }}" class="block rounded-lg px-3 py-2.5 text-base font-medium {{ $link['active'] ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-100' }}">{{ $link['label'] }}</a>
        @endforeach
        <div class="mt-3 border-t border-slate-100 pt-3">
            @guest
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
                    <a href="{{ route('register') }}" class="btn-primary">Sign up</a>
                </div>
            @else
                @if ($user->isStaff())
                    <a href="{{ route('portal.dashboard') }}" class="block rounded-lg px-3 py-2.5 font-medium text-slate-700 hover:bg-slate-100">Staff portal</a>
                @endif
                <a href="{{ route('profile.show') }}" class="block rounded-lg px-3 py-2.5 font-medium text-slate-700 hover:bg-slate-100">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full rounded-lg px-3 py-2.5 text-left font-medium text-slate-700 hover:bg-slate-100">Log out</button>
                </form>
            @endguest
        </div>
    </div>
</header>

<main class="flex-1">
    @isset($header)
        <div class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">{{ $header }}</div>
    @endisset
    {{ $slot }}
</main>

<footer class="mt-16 border-t border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-6 text-sm text-slate-500 sm:flex-row sm:px-6 lg:px-8">
        <x-logo size="sm" />
        <p>&copy; {{ date('Y') }} BUSina · Group 5 Laravel Project</p>
    </div>
</footer>

@stack('modals')
@livewireScripts
</body>
</html>
