<x-site-layout title="Select Seats">
    @php
        $user = auth()->user();
        $fare = (float) $schedule->route->fare;
        $oldPassengers = collect(old('passengers', []))
            ->map(fn ($p) => ['seat_number' => (int) ($p['seat_number'] ?? 0), 'passenger_name' => (string) ($p['passenger_name'] ?? '')])
            ->filter(fn ($p) => $p['seat_number'] > 0)
            ->values();
        $seatsLeft = $schedule->availableSeats();
        $max = min(max($passengers, $oldPassengers->count()), max($seatsLeft, 1));
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8"
         x-data="seatPicker({ max: {{ $max }}, taken: @js($takenSeats), initial: @js($oldPassengers) })">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('trips.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800">
            ← Back to results
        </a>
        <x-flash />

        {{-- Trip summary --}}
        <div class="card mb-6 flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xl font-bold text-slate-900">{{ $schedule->route->departure_location }} → {{ $schedule->route->destination }}</p>
                <p class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                    <span class="inline-flex items-center gap-1"><x-icon name="calendar" class="h-4 w-4" /> {{ $schedule->departsAt()->format('l, F j, Y') }}</span>
                    <span class="inline-flex items-center gap-1"><x-icon name="clock" class="h-4 w-4" /> {{ $schedule->departsAt()->format('g:i A') }} – {{ $schedule->arrivesAt()->format('g:i A') }} ({{ $schedule->route->durationLabel() }})</span>
                    <span class="inline-flex items-center gap-1"><x-icon name="bus" class="h-4 w-4" /> {{ $schedule->bus->typeLabel() }} · {{ $schedule->bus->bus_number }}</span>
                </p>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-2xl font-bold text-brand-600">₱{{ number_format($fare, 2) }}</p>
                <p class="text-xs text-slate-500">per seat · {{ $seatsLeft }} seats left</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[auto_1fr]">
            {{-- Seat map --}}
            <div class="card flex flex-col items-center p-5 sm:p-6">
                <h2 class="mb-4 self-start text-lg font-semibold text-slate-900">Choose your seats</h2>
                <x-seat-map :capacity="$schedule->bus->seating_capacity" :taken="$takenSeats" interactive />
                <p x-show="notice" x-text="notice" x-cloak class="mt-4 max-w-xs rounded-lg bg-amber-50 px-3 py-2 text-center text-sm text-amber-800"></p>
            </div>

            {{-- Booking form --}}
            <div class="card p-5 sm:p-6">
                @guest
                    <h2 class="text-lg font-semibold text-slate-900">Log in to book</h2>
                    <p class="mt-1 text-sm text-slate-500">You can look at the seat map now. To reserve, log in or create a free passenger account.</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="btn-primary">Log in</a>
                        <a href="{{ route('register') }}" class="btn-secondary">Create account</a>
                    </div>
                @elseif (! $user->isCustomer())
                    <h2 class="text-lg font-semibold text-slate-900">Staff preview</h2>
                    <p class="mt-1 text-sm text-slate-500">Staff accounts can view seats but cannot book. Use a customer account to make a reservation.</p>
                    <a href="{{ route('portal.schedules.show', $schedule) }}" class="btn-secondary mt-5">Open in staff portal</a>
                @else
                    <form method="POST" action="{{ route('customer.reservations.store', $schedule) }}" novalidate>
                        @csrf
                        <div class="flex flex-wrap items-end justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Passenger details</h2>
                                <p class="text-sm text-slate-500">
                                    <span x-text="passengers.length"></span> of <span x-text="max"></span> seat(s) selected
                                </p>
                            </div>
                            <div class="w-36">
                                <label for="pax" class="form-label">Passengers<span class="text-red-600"> *</span></label>
                                <select id="pax" class="field" :value="max" @change="setMax($event.target.value)">
                                    @foreach (range(1, min(\App\Services\ReservationService::MAX_SEATS_PER_BOOKING, max($seatsLeft, 1))) as $n)
                                        <option value="{{ $n }}" @selected($n === $max)>{{ $n }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-5 space-y-3">
                            <template x-if="passengers.length === 0">
                                <div class="rounded-xl border-2 border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                                    Tap an available seat on the map to add a passenger.
                                </div>
                            </template>
                            <template x-for="(p, i) in passengers" :key="p.seat_number">
                                <div class="flex items-start gap-3 rounded-xl border border-slate-200 p-3">
                                    <span class="mt-6 inline-flex h-10 w-12 shrink-0 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white" x-text="p.seat_number"></span>
                                    <input type="hidden" :name="`passengers[${i}][seat_number]`" :value="p.seat_number">
                                    <div class="flex-1">
                                        <label class="form-label" :for="`pname_${p.seat_number}`">Passenger name (seat <span x-text="p.seat_number"></span>)<span class="text-red-600"> *</span></label>
                                        <input type="text" class="field" :id="`pname_${p.seat_number}`" :name="`passengers[${i}][passenger_name]`" x-model="p.passenger_name" maxlength="100" placeholder="Full name">
                                        <p class="mt-1 text-sm text-red-600" x-show="i === 0 && {{ $errors->has('passengers.*.passenger_name') ? 'true' : 'false' }} && !p.passenger_name" x-cloak>Enter a name for every passenger.</p>
                                    </div>
                                    <button type="button" @click="toggle(p.seat_number)" class="mt-6 rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-red-600" :aria-label="`Remove seat ${p.seat_number}`">
                                        <x-icon name="x" class="h-5 w-5" />
                                    </button>
                                </div>
                            </template>
                            @error('passengers.*.seat_number') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-6 grid gap-4 border-t border-slate-100 pt-6 sm:grid-cols-2">
                            <x-form.input name="contact_name" label="Contact person" :value="$user->name" required autocomplete="name" />
                            <x-form.input name="contact_number" type="tel" label="Mobile number" required placeholder="09171234567" inputmode="tel" autocomplete="tel"
                                          hint="Philippine mobile number: 09XXXXXXXXX or +639XXXXXXXXX" />
                        </div>

                        <div class="mt-6 flex flex-col gap-4 rounded-xl bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-slate-500">Total fare</p>
                                <p class="text-2xl font-bold text-slate-900"
                                   x-text="'₱' + ({{ $fare }} * passengers.length).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">₱0.00</p>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn-primary w-full sm:w-auto" :disabled="passengers.length !== max">Reserve seats</button>
                                <p class="mt-1 text-xs text-slate-500" x-show="passengers.length !== max">
                                    Select <span x-text="max - passengers.length"></span> more seat(s) to continue.
                                </p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">Your reservation will be <strong>Pending Approval</strong> until a BUSina staff member confirms it.</p>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</x-site-layout>
