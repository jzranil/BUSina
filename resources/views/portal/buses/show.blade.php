<x-app-layout :title="'Bus '.$bus->bus_number">
    <x-page-header :title="'Bus '.$bus->bus_number" :subtitle="$bus->typeLabel().' · '.$bus->seating_capacity.' seats'">
        <a href="{{ route('portal.buses.edit', $bus) }}" class="btn-secondary"><x-icon name="pencil" class="h-4 w-4" /> Edit</a>
        <a href="{{ route('portal.schedules.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Schedule a trip</a>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-5">
            <h2 class="font-semibold text-slate-900">Details</h2>
            <dl class="mt-4 space-y-3 text-sm">
                @foreach ([
                    'Plate number' => $bus->plate_number,
                    'Type' => $bus->typeLabel(),
                    'Seating capacity' => $bus->seating_capacity,
                    'Driver' => $bus->driver_name,
                    'Added' => $bus->created_at->format('M j, Y'),
                ] as $label => $value)
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">{{ $label }}</dt><dd class="font-medium text-slate-900">{{ $value }}</dd></div>
                @endforeach
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Status</dt><dd><x-badge :status="$bus->status">{{ $bus->statusLabel() }}</x-badge></dd></div>
            </dl>
        </div>

        <div class="card overflow-hidden lg:col-span-2">
            <h2 class="border-b border-slate-100 px-5 py-4 font-semibold text-slate-900">Upcoming trips</h2>
            @if ($bus->schedules->isEmpty())
                <x-empty-state icon="calendar" title="No upcoming trips" message="This bus is not assigned to any future trip." />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Date</th><th>Time</th><th>Route</th><th>Booked</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach ($bus->schedules as $schedule)
                                <tr>
                                    <td><a href="{{ route('portal.schedules.show', $schedule) }}" class="font-medium text-brand-700 hover:underline">{{ $schedule->departure_date->format('D, M j') }}</a></td>
                                    <td>{{ $schedule->departsAt()->format('g:i A') }}</td>
                                    <td>{{ $schedule->route->route_name }}</td>
                                    <td>{{ $schedule->held_seats_count }} / {{ $bus->seating_capacity }}</td>
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
