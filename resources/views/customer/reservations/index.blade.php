<x-site-layout title="My Reservations">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <x-flash />
        <x-page-header title="My reservations">
            <a href="{{ route('trips.index') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> New booking</a>
        </x-page-header>

        <div class="mb-5 flex gap-2 overflow-x-auto pb-1">
            @foreach (['' => 'All'] + $statuses as $value => $label)
                @php $active = (string) request('status', '') === (string) $value; @endphp
                <a href="{{ route('customer.reservations.index', array_filter(['status' => $value])) }}"
                   class="whitespace-nowrap rounded-full px-4 py-1.5 text-sm font-medium {{ $active ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="space-y-3">
            @forelse ($reservations as $reservation)
                @include('customer._reservation-card')
            @empty
                <div class="card">
                    <x-empty-state icon="ticket" :title="request('status') ? 'No reservations with this status' : 'No reservations yet'" message="Find a trip and pick your seat to get started.">
                        <a href="{{ route('trips.index') }}" class="btn-primary">Search trips</a>
                    </x-empty-state>
                </div>
            @endforelse
        </div>
        <div class="mt-6">{{ $reservations->links() }}</div>
    </div>
</x-site-layout>
