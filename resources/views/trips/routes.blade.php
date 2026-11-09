<x-site-layout title="Routes">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-page-header title="Routes" subtitle="Every route BUSina serves, with fare per seat and travel time." />

        @if ($routes->isEmpty())
            <div class="card"><x-empty-state icon="map" title="No routes yet" /></div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($routes as $route)
                    <div class="card flex flex-col p-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $route->route_name }}</p>
                        <div class="mt-2 flex items-center gap-2 text-lg font-semibold text-slate-900">
                            <x-icon name="pin" class="h-5 w-5 text-brand-500" />
                            {{ $route->departure_location }}
                            <x-icon name="arrow-right" class="h-4 w-4 text-slate-400" />
                            {{ $route->destination }}
                        </div>
                        <dl class="mt-4 grid grid-cols-3 gap-2 text-sm">
                            <div><dt class="text-slate-500">Fare</dt><dd class="font-semibold text-brand-600">₱{{ number_format($route->fare, 0) }}</dd></div>
                            <div><dt class="text-slate-500">Travel time</dt><dd class="font-semibold text-slate-800">{{ $route->durationLabel() }}</dd></div>
                            <div><dt class="text-slate-500">Distance</dt><dd class="font-semibold text-slate-800">{{ (float) $route->distance }} km</dd></div>
                        </dl>
                        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                            <span class="text-sm text-slate-500">{{ $route->upcoming_trips_count }} upcoming {{ \Illuminate\Support\Str::plural('trip', $route->upcoming_trips_count) }}</span>
                            <a href="{{ route('trips.index', ['from' => $route->departure_location, 'to' => $route->destination]) }}" class="btn-secondary btn-sm">View trips</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-site-layout>
