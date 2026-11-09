<x-app-layout title="Reservations">
    <x-page-header title="Reservations" subtitle="Pending requests are listed first, oldest at the top." />

    @php $filtered = request()->hasAny(['q', 'status', 'schedule_id']); @endphp

    {{-- Status tabs --}}
    <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
        @foreach (['' => 'All'] + $statuses as $value => $label)
            @php $active = (string) request('status', '') === (string) $value; @endphp
            <a href="{{ route('portal.reservations.index', array_filter(['status' => $value, 'q' => request('q')])) }}"
               class="whitespace-nowrap rounded-full px-4 py-1.5 text-sm font-medium {{ $active ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="card">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search booking code, name or mobile number" class="field pl-9" aria-label="Search reservations">
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary">Search</button>
                @if ($filtered)
                    <a href="{{ route('portal.reservations.index') }}" class="btn-ghost">Clear</a>
                @endif
            </div>
        </form>

        @if ($reservations->isEmpty())
            <x-empty-state icon="ticket" :title="$filtered ? 'No reservations match' : 'No reservations yet'" message="Customer bookings will show up here." />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Booking</th><th>Passenger contact</th><th>Trip</th><th>Seats</th><th>Fare</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td>
                                    <a href="{{ route('portal.reservations.show', $reservation) }}" class="font-mono font-semibold text-brand-700 hover:underline">{{ $reservation->booking_code }}</a>
                                    <p class="text-xs text-slate-500">{{ $reservation->created_at->diffForHumans() }}</p>
                                </td>
                                <td>
                                    <p class="font-medium text-slate-900">{{ $reservation->contact_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $reservation->contact_number }}</p>
                                </td>
                                <td>
                                    <p>{{ $reservation->schedule->route->departure_location }} → {{ $reservation->schedule->route->destination }}</p>
                                    <p class="text-xs text-slate-500">{{ $reservation->schedule->departsAt()->format('M j, g:i A') }} · {{ $reservation->schedule->bus->bus_number }}</p>
                                </td>
                                <td>{{ $reservation->seats->pluck('seat_number')->implode(', ') }}</td>
                                <td class="font-semibold">₱{{ number_format($reservation->total_fare, 2) }}</td>
                                <td><x-badge :status="$reservation->status">{{ $reservation->statusLabel() }}</x-badge></td>
                                <td>
                                    <div class="flex justify-end gap-1">
                                        @if ($reservation->isPending())
                                            <x-confirm-action :action="route('portal.reservations.approve', $reservation)" method="PATCH" variant="success"
                                                              title="Approve {{ $reservation->booking_code }}?" message="The customer will see this booking as Approved." confirm="Approve"
                                                              button-class="btn-ghost btn-sm text-emerald-700 hover:bg-emerald-50" title="Approve">
                                                <x-icon name="check" class="h-4 w-4" /><span class="sr-only">Approve</span>
                                            </x-confirm-action>
                                            <x-confirm-action :action="route('portal.reservations.reject', $reservation)" method="PATCH" :remarks="true"
                                                              title="Reject {{ $reservation->booking_code }}?" message="The seats will be released for other passengers." confirm="Reject"
                                                              button-class="btn-ghost btn-sm text-red-600 hover:bg-red-50" title="Reject">
                                                <x-icon name="x" class="h-4 w-4" /><span class="sr-only">Reject</span>
                                            </x-confirm-action>
                                        @endif
                                        <a href="{{ route('portal.reservations.show', $reservation) }}" class="btn-ghost btn-sm" title="View"><x-icon name="eye" class="h-4 w-4" /><span class="sr-only">View</span></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($reservations->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $reservations->links() }}</div>
            @endif
        @endif
    </div>
</x-app-layout>
