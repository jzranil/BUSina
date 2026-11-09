{{-- One reservation as a ticket-style card. Needs $reservation (with schedule.route, schedule.bus, seats). --}}
@php $trip = $reservation->schedule; @endphp
<a href="{{ route('customer.reservations.show', $reservation) }}"
   class="card group flex flex-col overflow-hidden transition hover:border-brand-300 hover:shadow-md sm:flex-row">
    <div class="flex items-center justify-between gap-4 bg-brand-50 px-5 py-4 sm:w-44 sm:flex-col sm:items-start sm:justify-center">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-700">{{ $trip->departsAt()->format('D') }}</p>
            <p class="text-2xl font-bold text-slate-900">{{ $trip->departsAt()->format('M j') }}</p>
            <p class="text-sm font-medium text-slate-600">{{ $trip->departsAt()->format('g:i A') }}</p>
        </div>
        <x-badge :status="$reservation->status" class="sm:mt-2">{{ $reservation->statusLabel() }}</x-badge>
    </div>
    <div class="flex flex-1 flex-col justify-center gap-1 border-t border-dashed border-slate-200 px-5 py-4 sm:border-l sm:border-t-0">
        <p class="font-semibold text-slate-900">{{ $trip->route->departure_location }} → {{ $trip->route->destination }}</p>
        <p class="text-sm text-slate-500">
            Seat {{ $reservation->seats->pluck('seat_number')->implode(', ') }} · {{ $trip->bus->typeLabel() }} · {{ $trip->bus->bus_number }}
        </p>
        <p class="font-mono text-xs text-slate-400">{{ $reservation->booking_code }}</p>
    </div>
    <div class="flex items-center justify-between gap-4 border-t border-slate-100 px-5 py-4 sm:border-t-0">
        <p class="text-lg font-bold text-slate-900">₱{{ number_format($reservation->total_fare, 2) }}</p>
        <x-icon name="chevron-right" class="h-5 w-5 text-slate-400 transition group-hover:translate-x-1 group-hover:text-brand-600" />
    </div>
</a>
