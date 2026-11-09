<x-site-layout>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-600 via-brand-700 to-slate-900">
        <x-steering-wheel class="pointer-events-none absolute -right-32 -top-32 h-[28rem] w-[28rem] text-white/10" stroke-width="1" />
        <div class="relative mx-auto max-w-7xl px-4 pb-32 pt-16 sm:px-6 sm:pt-20 lg:px-8">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-sm font-medium text-brand-50 ring-1 ring-white/20">
                    <x-icon name="bus" class="h-4 w-4" /> {{ $tripCount }} upcoming trips open for booking
                </span>
                <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
                    Book your bus seat in minutes.
                </h1>
                <p class="mt-4 text-lg text-brand-100">
                    Find a trip, pick the exact seat you want on the bus map, and get notified once your reservation is approved.
                </p>
            </div>
        </div>
    </section>

    {{-- Search card overlapping the hero --}}
    <section class="relative z-10 mx-auto -mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="card p-5 shadow-xl sm:p-6">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Where are you headed?</h2>
            @include('trips._search')
        </div>
    </section>

    {{-- Popular routes --}}
    <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Popular routes</h2>
                <p class="mt-1 text-sm text-slate-500">Fixed fares per seat. No hidden fees.</p>
            </div>
            <a href="{{ route('routes.list') }}" class="hidden text-sm font-semibold text-brand-600 hover:text-brand-700 sm:inline-flex sm:items-center sm:gap-1">
                All routes <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($popularRoutes as $route)
                <a href="{{ route('trips.index', ['from' => $route->departure_location, 'to' => $route->destination]) }}"
                   class="card group p-5 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">{{ $route->durationLabel() }} · {{ (float) $route->distance }} km</span>
                        <span class="text-lg font-bold text-brand-600">₱{{ number_format($route->fare, 0) }}</span>
                    </div>
                    <div class="mt-3 flex items-center gap-3 text-lg font-semibold text-slate-900">
                        <span>{{ $route->departure_location }}</span>
                        <x-icon name="arrow-right" class="h-5 w-5 text-slate-400 transition group-hover:translate-x-1 group-hover:text-brand-500" />
                        <span>{{ $route->destination }}</span>
                    </div>
                </a>
            @empty
                <div class="card sm:col-span-2 lg:col-span-3">
                    <x-empty-state icon="map" title="No routes yet" message="Routes will appear here once staff add them." />
                </div>
            @endforelse
        </div>
    </section>

    {{-- How it works --}}
    <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-slate-900">How it works</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-4">
            @foreach ([
                ['search', 'Search', 'Choose where you are going and when.'],
                ['bus', 'Pick your seat', 'See which seats are free on the 2+2 seat map.'],
                ['ticket', 'Reserve', 'Enter passenger names and your mobile number.'],
                ['check-circle', 'Get approved', 'Staff confirm your booking. Track it in My Reservations.'],
            ] as $i => [$icon, $title, $text])
                <div class="card p-5">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><x-icon :name="$icon" class="h-5 w-5" /></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Step {{ $i + 1 }}</span>
                    </div>
                    <h3 class="mt-4 font-semibold text-slate-900">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>
</x-site-layout>
