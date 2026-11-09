<x-app-layout title="Schedules">
    <x-page-header title="Schedules" subtitle="Trips: a bus running a route at a set date and time.">
        <a href="{{ route('portal.schedules.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> New trip</a>
    </x-page-header>

    @php $filtered = request()->hasAny(['date', 'route_id', 'bus_id', 'status']); @endphp

    <div class="card">
        <form method="GET" class="grid gap-3 border-b border-slate-100 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_1fr_auto]">
            <input type="date" name="date" value="{{ request('date') }}" class="field" aria-label="Filter by date">
            <select name="route_id" class="field" aria-label="Filter by route">
                <option value="">All routes</option>
                @foreach ($routes as $r)
                    <option value="{{ $r->route_id }}" @selected((string) request('route_id') === (string) $r->route_id)>{{ $r->route_name }}</option>
                @endforeach
            </select>
            <select name="bus_id" class="field" aria-label="Filter by bus">
                <option value="">All buses</option>
                @foreach ($buses as $b)
                    <option value="{{ $b->bus_id }}" @selected((string) request('bus_id') === (string) $b->bus_id)>{{ $b->bus_number }}</option>
                @endforeach
            </select>
            <select name="status" class="field" aria-label="Filter by status">
                <option value="">All statuses</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="btn-secondary"><x-icon name="filter" class="h-4 w-4" /> Filter</button>
                @if ($filtered)
                    <a href="{{ route('portal.schedules.index') }}" class="btn-ghost">Clear</a>
                @endif
            </div>
        </form>

        @if ($schedules->isEmpty())
            <x-empty-state icon="calendar" :title="$filtered ? 'No trips match your filters' : 'No trips yet'" message="Schedule a trip by picking a bus, a route and a departure time.">
                <a href="{{ route('portal.schedules.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> New trip</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr><th>Departure</th><th>Route</th><th>Bus</th><th>Arrival</th><th>Booked</th><th>Status</th><th class="text-right">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            @php $pct = round($schedule->held_seats_count / max(1, $schedule->bus->seating_capacity) * 100); @endphp
                            <tr>
                                <td>
                                    <p class="font-semibold text-slate-900">{{ $schedule->departure_date->format('D, M j, Y') }}</p>
                                    <p class="text-xs text-slate-500">{{ $schedule->departsAt()->format('g:i A') }}</p>
                                </td>
                                <td>{{ $schedule->route->route_name }}</td>
                                <td>{{ $schedule->bus->bus_number }} <span class="text-xs text-slate-500">({{ $schedule->bus->typeLabel() }})</span></td>
                                <td>{{ $schedule->arrivesAt()->format('g:i A') }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-20 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-brand-500" style="width: {{ $pct }}%"></div></div>
                                        <span class="text-xs text-slate-600">{{ $schedule->held_seats_count }}/{{ $schedule->bus->seating_capacity }}</span>
                                    </div>
                                </td>
                                <td><x-badge :status="$schedule->status">{{ $schedule->statusLabel() }}</x-badge></td>
                                <td>
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('portal.schedules.show', $schedule) }}" class="btn-ghost btn-sm" title="View"><x-icon name="eye" class="h-4 w-4" /><span class="sr-only">View</span></a>
                                        <a href="{{ route('portal.schedules.edit', $schedule) }}" class="btn-ghost btn-sm" title="Edit"><x-icon name="pencil" class="h-4 w-4" /><span class="sr-only">Edit</span></a>
                                        <x-confirm-action :action="route('portal.schedules.destroy', $schedule)" method="DELETE"
                                                          title="Delete this trip?" message="Trips that already have reservations cannot be deleted; set them to Canceled instead." confirm="Delete"
                                                          button-class="btn-ghost btn-sm text-red-600 hover:bg-red-50 hover:text-red-700" title="Delete">
                                            <x-icon name="trash" class="h-4 w-4" /><span class="sr-only">Delete</span>
                                        </x-confirm-action>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($schedules->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $schedules->links() }}</div>
            @endif
        @endif
    </div>
</x-app-layout>
