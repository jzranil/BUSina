<x-app-layout title="Trip">
    <x-page-header :title="$schedule->route->departure_location.' → '.$schedule->route->destination"
                   :subtitle="$schedule->departsAt()->format('l, F j, Y · g:i A').' – '.$schedule->arrivesAt()->format('g:i A')">
        <x-badge :status="$schedule->status" class="text-sm">{{ $schedule->statusLabel() }}</x-badge>
        <a href="{{ route('portal.schedules.edit', $schedule) }}" class="btn-secondary"><x-icon name="pencil" class="h-4 w-4" /> Edit</a>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[auto_1fr]">
        <div class="card flex flex-col items-center p-5">
            <div class="mb-4 w-full">
                <h2 class="font-semibold text-slate-900">Seat map</h2>
                <p class="text-sm text-slate-500">{{ count($takenSeats) }} of {{ $schedule->bus->seating_capacity }} seats taken · {{ $schedule->bus->bus_number }} ({{ $schedule->bus->typeLabel() }})</p>
            </div>
            <x-seat-map :capacity="$schedule->bus->seating_capacity" :taken="$takenSeats" />
        </div>

        <div class="card overflow-hidden">
            <h2 class="border-b border-slate-100 px-5 py-4 font-semibold text-slate-900">Reservations on this trip</h2>
            @if ($schedule->reservations->isEmpty())
                <x-empty-state icon="ticket" title="No reservations yet" />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Booking</th><th>Contact</th><th>Seats</th><th>Fare</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach ($schedule->reservations as $reservation)
                                <tr>
                                    <td><a href="{{ route('portal.reservations.show', $reservation) }}" class="font-mono font-semibold text-brand-700 hover:underline">{{ $reservation->booking_code }}</a></td>
                                    <td>
                                        <p class="font-medium text-slate-900">{{ $reservation->contact_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $reservation->contact_number }}</p>
                                    </td>
                                    <td>{{ $reservation->seats->pluck('seat_number')->implode(', ') }}</td>
                                    <td>₱{{ number_format($reservation->total_fare, 2) }}</td>
                                    <td><x-badge :status="$reservation->status">{{ $reservation->statusLabel() }}</x-badge></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
