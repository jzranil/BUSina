<x-app-layout title="Buses">
    <x-page-header title="Buses" subtitle="The fleet. A bus is assigned to trips on the Schedules page.">
        <a href="{{ route('portal.buses.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add bus</a>
    </x-page-header>

    <div class="card">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search bus no., plate or driver" class="field pl-9" aria-label="Search buses">
            </div>
            <select name="status" class="field sm:w-48" aria-label="Filter by status">
                <option value="">All statuses</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="type" class="field sm:w-48" aria-label="Filter by type">
                <option value="">All types</option>
                @foreach ($types as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="btn-secondary"><x-icon name="filter" class="h-4 w-4" /> Filter</button>
                @if (request()->hasAny(['q', 'status', 'type']))
                    <a href="{{ route('portal.buses.index') }}" class="btn-ghost">Clear</a>
                @endif
            </div>
        </form>

        @if ($buses->isEmpty())
            <x-empty-state icon="bus" :title="request()->hasAny(['q', 'status', 'type']) ? 'No buses match your filters' : 'No buses yet'"
                           message="Add a bus to start scheduling trips.">
                <a href="{{ route('portal.buses.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add bus</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Bus no.</th><th>Plate</th><th>Type</th><th>Seats</th><th>Driver</th><th>Status</th><th>Trips</th><th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($buses as $bus)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $bus->bus_number }}</td>
                                <td class="font-mono">{{ $bus->plate_number }}</td>
                                <td>{{ $bus->typeLabel() }}</td>
                                <td>{{ $bus->seating_capacity }}</td>
                                <td>{{ $bus->driver_name }}</td>
                                <td><x-badge :status="$bus->status">{{ $bus->statusLabel() }}</x-badge></td>
                                <td>{{ $bus->schedules_count }}</td>
                                <td>
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('portal.buses.show', $bus) }}" class="btn-ghost btn-sm" title="View"><x-icon name="eye" class="h-4 w-4" /><span class="sr-only">View {{ $bus->bus_number }}</span></a>
                                        <a href="{{ route('portal.buses.edit', $bus) }}" class="btn-ghost btn-sm" title="Edit"><x-icon name="pencil" class="h-4 w-4" /><span class="sr-only">Edit {{ $bus->bus_number }}</span></a>
                                        <x-confirm-action :action="route('portal.buses.destroy', $bus)" method="DELETE"
                                                          title="Delete bus {{ $bus->bus_number }}?" message="This cannot be undone. Buses with trips cannot be deleted." confirm="Delete"
                                                          button-class="btn-ghost btn-sm text-red-600 hover:bg-red-50 hover:text-red-700" title="Delete">
                                            <x-icon name="trash" class="h-4 w-4" /><span class="sr-only">Delete {{ $bus->bus_number }}</span>
                                        </x-confirm-action>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($buses->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $buses->links() }}</div>
            @endif
        @endif
    </div>
</x-app-layout>
