<x-site-layout title="Search Trips">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-flash />
        <x-page-header title="Search trips" subtitle="Showing trips that are still open for booking." />

        <div class="card mb-6 p-5">
            @include('trips._search')
        </div>

        @php $pax = (int) ($filters['passengers'] ?? 1); @endphp

        @if ($trips->isEmpty())
            <div class="card">
                <x-empty-state icon="bus" title="No trips found" message="Try another date or route, or clear the filters.">
                    <a href="{{ route('trips.index') }}" class="btn-secondary">Clear filters</a>
                </x-empty-state>
            </div>
        @else
            <p class="mb-3 text-sm text-slate-500">{{ $trips->total() }} {{ \Illuminate\Support\Str::plural('trip', $trips->total()) }} found</p>
            <div class="space-y-3">
                @foreach ($trips as $trip)
                    @php $seatsLeft = $trip->availableSeats(); @endphp
                    <div class="card flex flex-col gap-4 p-5 md:flex-row md:items-center">
                        <div class="flex items-center gap-4 md:w-60">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-slate-900">{{ $trip->departsAt()->format('g:i') }}<span class="text-sm font-semibold">{{ $trip->departsAt()->format(' A') }}</span></p>
                                <p class="text-xs text-slate-500">{{ $trip->departsAt()->format('D, M j') }}</p>
                            </div>
                            <div class="flex-1 border-t-2 border-dashed border-slate-200"></div>
                            <div class="text-center">
                                <p class="text-lg font-semibold text-slate-700">{{ $trip->arrivesAt()->format('g:i A') }}</p>
                                <p class="text-xs text-slate-500">{{ $trip->route->durationLabel() }}</p>
                            </div>
                        </div>

                        <div class="flex-1">
                            <p class="font-semibold text-slate-900">{{ $trip->route->departure_location }} → {{ $trip->route->destination }}</p>
                            <p class="mt-0.5 text-sm text-slate-500">{{ $trip->bus->typeLabel() }} · {{ $trip->bus->bus_number }}</p>
                        </div>

                        <div class="flex items-center justify-between gap-6 md:justify-end">
                            <div class="text-right">
                                <p class="text-xl font-bold text-brand-600">₱{{ number_format($trip->route->fare, 2) }}</p>
                                <p class="text-xs {{ $seatsLeft <= 5 ? 'font-semibold text-amber-600' : 'text-slate-500' }}">
                                    {{ $seatsLeft }} {{ \Illuminate\Support\Str::plural('seat', $seatsLeft) }} left
                                </p>
                            </div>
                            <a href="{{ route('trips.show', ['schedule' => $trip, 'passengers' => $pax]) }}" class="btn-primary">
                                Select seats
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $trips->links() }}</div>
        @endif
    </div>
</x-site-layout>
