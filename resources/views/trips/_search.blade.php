{{-- Trip search widget (landing page hero and search page). Needs $origins, $destinations, optional $filters. --}}
@php $filters = $filters ?? []; @endphp
<form method="GET" action="{{ route('trips.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_9rem_auto] lg:items-end" novalidate>
    <div>
        <label for="s_from" class="form-label">From</label>
        <select id="s_from" name="from" class="field">
            <option value="">Any</option>
            @foreach ($origins as $origin)
                <option value="{{ $origin }}" @selected(($filters['from'] ?? '') === $origin)>{{ $origin }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="s_to" class="form-label">To</label>
        <select id="s_to" name="to" class="field">
            <option value="">Any</option>
            @foreach ($destinations as $destination)
                <option value="{{ $destination }}" @selected(($filters['to'] ?? '') === $destination)>{{ $destination }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="s_date" class="form-label">Travel date</label>
        <input id="s_date" type="date" name="date" min="{{ today()->toDateString() }}" value="{{ $filters['date'] ?? '' }}"
               class="field @error('date') field-error @enderror">
        @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="s_pax" class="form-label">Passengers</label>
        <select id="s_pax" name="passengers" class="field">
            @foreach (range(1, \App\Services\ReservationService::MAX_SEATS_PER_BOOKING) as $n)
                <option value="{{ $n }}" @selected((int) ($filters['passengers'] ?? 1) === $n)>{{ $n }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn-primary h-[42px] sm:col-span-2 lg:col-span-1">
        <x-icon name="search" class="h-5 w-5" /> Search trips
    </button>
</form>
