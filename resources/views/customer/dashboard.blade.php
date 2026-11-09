<x-site-layout title="My Dashboard">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-flash />
        <x-page-header :title="'Hi, '.\Illuminate\Support\Str::before(auth()->user()->name, ' ').'!'" subtitle="Your trips at a glance.">
            <a href="{{ route('trips.index') }}" class="btn-primary"><x-icon name="search" class="h-4 w-4" /> Book a trip</a>
        </x-page-header>

        <div class="grid gap-4 sm:grid-cols-3">
            <x-stat-card label="Upcoming trips" :value="$upcoming->count()" icon="calendar" tone="sky" />
            <x-stat-card label="Pending approval" :value="$counts->get('pending', 0)" icon="clock" tone="amber" />
            <x-stat-card label="Approved bookings" :value="$counts->get('approved', 0)" icon="check-circle" tone="emerald" />
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <section class="lg:col-span-2">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Upcoming trips</h2>
                    <a href="{{ route('customer.reservations.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">All reservations</a>
                </div>
                <div class="space-y-3">
                    @forelse ($upcoming as $reservation)
                        @include('customer._reservation-card')
                    @empty
                        <div class="card">
                            <x-empty-state icon="ticket" title="No upcoming trips" message="When you book a seat, it will show up here.">
                                <a href="{{ route('trips.index') }}" class="btn-primary">Search trips</a>
                            </x-empty-state>
                        </div>
                    @endforelse
                </div>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-slate-900">Popular routes</h2>
                <div class="space-y-3">
                    @foreach ($popularRoutes as $route)
                        <a href="{{ route('trips.index', ['from' => $route->departure_location, 'to' => $route->destination]) }}"
                           class="card flex items-center justify-between p-4 hover:border-brand-300">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $route->departure_location }} → {{ $route->destination }}</p>
                                <p class="text-xs text-slate-500">{{ $route->durationLabel() }}</p>
                            </div>
                            <span class="font-bold text-brand-600">₱{{ number_format($route->fare, 0) }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-site-layout>
