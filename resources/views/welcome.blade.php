<x-site-layout>
    {{-- Hero --}}
    <section class="bg-ink">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 pb-28 pt-14 sm:px-6 sm:pt-20 lg:grid-cols-2 lg:px-8 lg:pb-32">
            <div>
                <span class="inline-flex rounded-full bg-brand-800 px-3 py-1 text-xs font-semibold text-white">Philippines’ easiest bus booking</span>
                <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] tracking-tight text-white sm:text-5xl">
                    Your Journey Starts<br class="hidden sm:block"> with <span class="text-brand-500">BUSina</span>.
                </h1>
                <p class="mt-5 max-w-md text-base text-slate-200">
                    Search trips, pick your favorite seat and reserve in minutes — no queues at the terminal.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('trips.index') }}" class="btn-primary">Get started</a>
                        <a href="{{ route('dashboard') }}" class="btn-secondary border-white">My dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="btn-primary">Get started</a>
                        <a href="{{ route('login') }}" class="btn-secondary border-white">Login</a>
                    @endauth
                </div>
            </div>
            <div class="mx-auto w-full max-w-md lg:max-w-lg">
                <x-bus-illustration class="w-full drop-shadow-xl" />
            </div>
        </div>
    </section>

    {{-- Search card overlapping the hero --}}
    <section class="relative z-10 mx-auto -mt-16 max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-lg sm:p-5">
            @include('trips._search', ['defaultDate' => today()->toDateString()])
        </div>
    </section>

    {{-- Popular routes --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-slate-900">Popular routes</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($popularRoutes as $route)
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-5 py-5 shadow-sm">
                    <div class="min-w-0">
                        <p class="truncate font-bold text-slate-900">{{ $route->departure_location }} – {{ $route->destination }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ (float) $route->distance }} km · {{ $route->durationLabel() }}</p>
                    </div>
                    <a href="{{ route('trips.index', ['from' => $route->departure_location, 'to' => $route->destination]) }}"
                       class="btn-primary shrink-0 px-5" aria-label="Book {{ $route->departure_location }} to {{ $route->destination }}">Book</a>
                </div>
            @empty
                <div class="card sm:col-span-2 lg:col-span-3">
                    <x-empty-state icon="map" title="No routes yet" message="Routes will appear here once staff add them." />
                </div>
            @endforelse
        </div>
    </section>

    {{-- How it works --}}
    <section class="bg-blush py-12 sm:py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-2xl font-bold text-slate-900">How it works</h2>
            <div class="mt-8 grid gap-4 md:grid-cols-3">
                @foreach ([
                    ['search', '1. Search', 'Pick your route and date.'],
                    ['ticket', '2. Reserve', 'Choose a seat and submit.'],
                    ['bus', '3. Travel', 'Get approved and board.'],
                ] as [$icon, $title, $text])
                    <div class="rounded-2xl bg-white px-6 py-6 text-center shadow-sm">
                        <span class="mx-auto inline-flex h-10 w-10 items-center justify-center rounded-xl bg-brand-800 text-white">
                            <x-icon :name="$icon" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-3 font-bold text-slate-900">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Highlights --}}
    <section class="bg-[#f8f5f6] py-10">
        <div class="mx-auto grid max-w-7xl gap-4 px-4 sm:grid-cols-3 sm:px-6 lg:px-8">
            @foreach (['Live seat maps', 'Verified drivers & buses', 'Easy cancellation'] as $point)
                <p class="flex items-center gap-3 text-sm font-semibold text-slate-900">
                    <x-icon name="check" class="h-5 w-5 text-brand-500" stroke-width="2" /> {{ $point }}
                </p>
            @endforeach
        </div>
    </section>
</x-site-layout>
