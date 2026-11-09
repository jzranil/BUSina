<x-app-layout :title="$busRoute->route_name">
    <x-page-header :title="$busRoute->route_name" :subtitle="$busRoute->departure_location.' → '.$busRoute->destination">
        <a href="{{ route('portal.routes.edit', $busRoute) }}" class="btn-secondary"><x-icon name="pencil" class="h-4 w-4" /> Edit</a>
        <a href="{{ route('portal.schedules.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Schedule a trip</a>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-5">
            <h2 class="font-semibold text-slate-900">Details</h2>
            <dl class="mt-4 space-y-3 text-sm">
                @foreach ([
                    'Departure' => $busRoute->departure_location,
                    'Destination' => $busRoute->destination,
                    'Distance' => (float) $busRoute->distance.' km',
                    'Travel time' => $busRoute->durationLabel(),
                    'Fare per seat' => '₱'.number_format($busRoute->fare, 2),
                ] as $label => $value)
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">{{ $label }}</dt><dd class="font-medium text-slate-900">{{ $value }}</dd></div>
                @endforeach
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Status</dt><dd><x-badge :status="$busRoute->is_active ? 'active' : 'inactive'">{{ $busRoute->is_active ? 'Active' : 'Inactive' }}</x-badge></dd></div>
            </dl>
        </div>

        <div class="card overflow-hidden lg:col-span-2">
            <h2 class="border-b border-slate-100 px-5 py-4 font-semibold text-slate-900">Upcoming trips</h2>
            @if ($busRoute->schedules->isEmpty())
                <x-empty-state icon="calendar" title="No upcoming trips" message="No future trips are scheduled on this route." />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Date</th><th>Time</th><th>Bus</th><th>Booked</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach ($busRoute->schedules as $schedule)
                                <tr>
                                    <td><a href="{{ route('portal.schedules.show', $schedule) }}" class="font-medium text-brand-700 hover:underline">{{ $schedule->departure_date->format('D, M j') }}</a></td>
                                    <td>{{ $schedule->departsAt()->format('g:i A') }}</td>
                                    <td>{{ $schedule->bus->bus_number }}</td>
                                    <td>{{ $schedule->held_seats_count }} / {{ $schedule->bus->seating_capacity }}</td>
                                    <td><x-badge :status="$schedule->status">{{ $schedule->statusLabel() }}</x-badge></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
