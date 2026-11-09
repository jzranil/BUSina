<x-site-layout :title="$reservation->booking_code">
    @php
        $trip = $reservation->schedule;
        $justBooked = session('success') && $reservation->isPending() && $reservation->created_at->gt(now()->subMinutes(5));
        $messages = [
            'pending' => ['bg-amber-50 text-amber-800 border-amber-200', 'clock', 'Waiting for approval', 'A BUSina staff member will review your booking soon. Your seats are held for you in the meantime.'],
            'approved' => ['bg-emerald-50 text-emerald-800 border-emerald-200', 'check-circle', 'Your trip is confirmed', 'Show this booking code at the terminal before boarding.'],
            'rejected' => ['bg-red-50 text-red-800 border-red-200', 'x-circle', 'This booking was not approved', 'Your seats were released. You are welcome to book again.'],
            'canceled' => ['bg-slate-50 text-slate-700 border-slate-200', 'x-circle', 'This booking was canceled', 'The seats were released.'],
        ];
        [$tone, $icon, $heading, $text] = $messages[$reservation->status];
    @endphp

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <a href="{{ route('customer.reservations.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800">← My reservations</a>

        @if ($justBooked)
            {{-- Success screen right after booking --}}
            <div class="card mb-6 p-8 text-center">
                <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                    <x-icon name="check" class="h-9 w-9" />
                </span>
                <h1 class="mt-4 text-2xl font-bold text-slate-900">Reservation submitted!</h1>
                <p class="mt-2 text-slate-600">Your booking code is</p>
                <p class="mt-1 font-mono text-3xl font-bold tracking-wider text-brand-600">{{ $reservation->booking_code }}</p>
                <p class="mx-auto mt-3 max-w-md text-sm text-slate-500">It is now <strong>Pending Approval</strong>. You can follow its status here or in My Reservations.</p>
            </div>
        @else
            <x-flash />
            <div class="mb-6 flex items-start gap-3 rounded-xl border px-4 py-4 {{ $tone }}">
                <x-icon :name="$icon" class="h-6 w-6 shrink-0" />
                <div>
                    <p class="font-semibold">{{ $heading }}</p>
                    <p class="text-sm">{{ $text }}</p>
                    @if ($reservation->remarks)
                        <p class="mt-1 text-sm"><strong>Note from staff:</strong> {{ $reservation->remarks }}</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Ticket --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between bg-gradient-to-r from-brand-600 to-brand-700 px-6 py-4 text-white">
                <x-logo light size="sm" />
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wider text-brand-100">Booking code</p>
                    <p class="font-mono text-lg font-bold">{{ $reservation->booking_code }}</p>
                </div>
            </div>
            <div class="grid gap-6 p-6 sm:grid-cols-[1fr_auto_1fr] sm:items-center">
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400">From</p>
                    <p class="text-xl font-bold text-slate-900">{{ $trip->route->departure_location }}</p>
                    <p class="text-sm text-slate-600">{{ $trip->departsAt()->format('D, M j, Y · g:i A') }}</p>
                </div>
                <x-icon name="arrow-right" class="hidden h-6 w-6 text-slate-300 sm:block" />
                <div class="sm:text-right">
                    <p class="text-xs uppercase tracking-wider text-slate-400">To</p>
                    <p class="text-xl font-bold text-slate-900">{{ $trip->route->destination }}</p>
                    <p class="text-sm text-slate-600">Arrives about {{ $trip->arrivesAt()->format('g:i A') }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 border-t border-dashed border-slate-200 px-6 py-4 text-sm sm:grid-cols-4">
                <div><p class="text-slate-500">Status</p><x-badge :status="$reservation->status" class="mt-1">{{ $reservation->statusLabel() }}</x-badge></div>
                <div><p class="text-slate-500">Bus</p><p class="font-semibold text-slate-900">{{ $trip->bus->bus_number }}</p><p class="text-xs text-slate-500">{{ $trip->bus->typeLabel() }}</p></div>
                <div><p class="text-slate-500">Contact</p><p class="font-semibold text-slate-900">{{ $reservation->contact_name }}</p><p class="text-xs text-slate-500">{{ $reservation->contact_number }}</p></div>
                <div><p class="text-slate-500">Total fare</p><p class="text-lg font-bold text-slate-900">₱{{ number_format($reservation->total_fare, 2) }}</p></div>
            </div>
            <div class="border-t border-slate-100 px-6 py-4">
                <p class="mb-2 text-sm font-semibold text-slate-900">Passengers</p>
                <ul class="divide-y divide-slate-100">
                    @foreach ($reservation->seats as $seat)
                        <li class="flex items-center gap-3 py-2">
                            <span class="inline-flex h-8 w-10 items-center justify-center rounded-lg bg-brand-50 text-sm font-bold text-brand-700">{{ $seat->seat_number }}</span>
                            <span class="text-sm font-medium text-slate-800">{{ $seat->passenger_name }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
            @can('cancel', $reservation)
                <x-confirm-action :action="route('customer.reservations.cancel', $reservation)" method="PATCH"
                                  title="Cancel this reservation?" message="Your seats will be released and other passengers can book them. This cannot be undone."
                                  confirm="Yes, cancel it" button-class="btn-secondary text-red-600">
                    Cancel reservation
                </x-confirm-action>
            @else
                <span></span>
            @endcan
            <div class="flex gap-3">
                <button type="button" onclick="window.print()" class="btn-secondary">Print</button>
                <a href="{{ route('trips.index') }}" class="btn-primary">Book another trip</a>
            </div>
        </div>
    </div>
</x-site-layout>
