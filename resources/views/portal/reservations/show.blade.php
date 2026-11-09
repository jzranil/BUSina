<x-app-layout :title="$reservation->booking_code">
    @php $trip = $reservation->schedule; @endphp

    <x-page-header :title="'Reservation '.$reservation->booking_code" :subtitle="'Booked '.$reservation->created_at->format('M j, Y g:i A')">
        <x-badge :status="$reservation->status" class="text-sm">{{ $reservation->statusLabel() }}</x-badge>
        @if ($reservation->isPending())
            <x-confirm-action :action="route('portal.reservations.approve', $reservation)" method="PATCH" variant="success"
                              title="Approve this reservation?" message="The customer will see it as Approved." confirm="Approve" button-class="btn-success">
                <x-icon name="check" class="h-4 w-4" /> Approve
            </x-confirm-action>
            <x-confirm-action :action="route('portal.reservations.reject', $reservation)" method="PATCH" :remarks="true"
                              title="Reject this reservation?" message="Its seats will be released for other passengers." confirm="Reject" button-class="btn-danger">
                <x-icon name="x" class="h-4 w-4" /> Reject
            </x-confirm-action>
        @elseif (auth()->user()->can('cancel', $reservation))
            <x-confirm-action :action="route('portal.reservations.cancel', $reservation)" method="PATCH"
                              title="Cancel this reservation?" message="Its seats will be released for other passengers." confirm="Cancel reservation" button-class="btn-secondary">
                Cancel reservation
            </x-confirm-action>
        @endif
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <h2 class="font-semibold text-slate-900">Trip</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-slate-500">Route</p>
                    <p class="font-semibold text-slate-900">{{ $trip->route->departure_location }} → {{ $trip->route->destination }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Departure</p>
                    <p class="font-semibold text-slate-900">{{ $trip->departsAt()->format('D, M j, Y g:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Bus</p>
                    <p class="font-semibold text-slate-900">{{ $trip->bus->bus_number }} · {{ $trip->bus->typeLabel() }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Trip page</p>
                    <a href="{{ route('portal.schedules.show', $trip) }}" class="font-semibold text-brand-700 hover:underline">View seat map</a>
                </div>
            </div>

            <h2 class="mt-8 font-semibold text-slate-900">Passengers</h2>
            <div class="mt-3 overflow-hidden rounded-xl border border-slate-200">
                <table class="data-table">
                    <thead><tr><th>Seat</th><th>Passenger name</th><th class="text-right">Fare</th></tr></thead>
                    <tbody>
                        @foreach ($reservation->seats as $seat)
                            <tr>
                                <td><span class="inline-flex h-8 w-10 items-center justify-center rounded-lg bg-brand-50 font-bold text-brand-700">{{ $seat->seat_number }}</span></td>
                                <td class="font-medium text-slate-900">{{ $seat->passenger_name }}</td>
                                <td class="text-right">₱{{ number_format($trip->route->fare, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 flex justify-end text-lg font-bold text-slate-900">Total ₱{{ number_format($reservation->total_fare, 2) }}</div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="font-semibold text-slate-900">Contact</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-slate-500">Name</dt><dd class="font-medium text-slate-900">{{ $reservation->contact_name }}</dd></div>
                    <div><dt class="text-slate-500">Mobile</dt><dd class="font-medium text-slate-900"><a href="tel:{{ $reservation->contact_number }}" class="hover:underline">{{ $reservation->contact_number }}</a></dd></div>
                    <div><dt class="text-slate-500">Account</dt><dd class="font-medium text-slate-900">{{ $reservation->user?->email ?? 'Deleted account' }}</dd></div>
                </dl>
            </div>
            @if ($reservation->reviewed_at)
                <div class="card p-5">
                    <h2 class="font-semibold text-slate-900">Review</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="text-slate-500">{{ $reservation->status === 'rejected' ? 'Rejected' : 'Approved' }} by</dt><dd class="font-medium text-slate-900">{{ $reservation->reviewer?->name ?? 'Deleted account' }}</dd></div>
                        <div><dt class="text-slate-500">On</dt><dd class="font-medium text-slate-900">{{ $reservation->reviewed_at->format('M j, Y g:i A') }}</dd></div>
                        @if ($reservation->remarks)
                            <div><dt class="text-slate-500">Remarks</dt><dd class="font-medium text-slate-900">{{ $reservation->remarks }}</dd></div>
                        @endif
                    </dl>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
