<x-app-layout title="Routes">
    <x-page-header title="Routes" subtitle="Where buses travel, with the fare per seat.">
        <a href="{{ route('portal.routes.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add route</a>
    </x-page-header>

    <div class="card">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name, origin or destination" class="field pl-9" aria-label="Search routes">
            </div>
            <select name="active" class="field sm:w-44" aria-label="Filter by status">
                <option value="">All routes</option>
                <option value="1" @selected(request('active') === '1')>Active</option>
                <option value="0" @selected(request('active') === '0')>Inactive</option>
            </select>
            <div class="flex gap-2">
                <button class="btn-secondary"><x-icon name="filter" class="h-4 w-4" /> Filter</button>
                @if (request()->hasAny(['q', 'active']))
                    <a href="{{ route('portal.routes.index') }}" class="btn-ghost">Clear</a>
                @endif
            </div>
        </form>

        @if ($routes->isEmpty())
            <x-empty-state icon="map" :title="request()->hasAny(['q', 'active']) ? 'No routes match your filters' : 'No routes yet'" message="Add a route before scheduling trips.">
                <a href="{{ route('portal.routes.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add route</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr><th>Route</th><th>From → To</th><th>Distance</th><th>Travel time</th><th>Fare</th><th>Status</th><th>Trips</th><th class="text-right">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($routes as $busRoute)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $busRoute->route_name }}</td>
                                <td>{{ $busRoute->departure_location }} → {{ $busRoute->destination }}</td>
                                <td>{{ (float) $busRoute->distance }} km</td>
                                <td>{{ $busRoute->durationLabel() }}</td>
                                <td class="font-semibold">₱{{ number_format($busRoute->fare, 2) }}</td>
                                <td><x-badge :status="$busRoute->is_active ? 'active' : 'inactive'">{{ $busRoute->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                                <td>{{ $busRoute->schedules_count }}</td>
                                <td>
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('portal.routes.show', $busRoute) }}" class="btn-ghost btn-sm" title="View"><x-icon name="eye" class="h-4 w-4" /><span class="sr-only">View</span></a>
                                        <a href="{{ route('portal.routes.edit', $busRoute) }}" class="btn-ghost btn-sm" title="Edit"><x-icon name="pencil" class="h-4 w-4" /><span class="sr-only">Edit</span></a>
                                        <x-confirm-action :action="route('portal.routes.destroy', $busRoute)" method="DELETE"
                                                          title="Delete route {{ $busRoute->route_name }}?" message="This cannot be undone. Routes with trips cannot be deleted." confirm="Delete"
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
            @if ($routes->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $routes->links() }}</div>
            @endif
        @endif
    </div>
</x-app-layout>
