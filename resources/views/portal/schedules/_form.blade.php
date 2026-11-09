{{-- Shared add/edit form. $schedule is null when adding. Only active buses and routes are listed. --}}
@csrf
@php
    $busOptions = $buses->mapWithKeys(fn ($b) => [$b->bus_id => $b->bus_number.' · '.$b->typeLabel().' · '.$b->seating_capacity.' seats'])->all();
    $routeOptions = $routes->mapWithKeys(fn ($r) => [$r->route_id => $r->route_name.' · '.$r->durationLabel().' · ₱'.number_format($r->fare, 0)])->all();
    // Keep the current bus/route selectable when editing, even if it has since been set inactive
    if ($schedule && ! isset($busOptions[$schedule->bus_id])) {
        $busOptions[$schedule->bus_id] = $schedule->bus->bus_number.' (not active)';
    }
    if ($schedule && ! isset($routeOptions[$schedule->route_id])) {
        $routeOptions[$schedule->route_id] = $schedule->route->route_name.' (not active)';
    }
@endphp
<div class="grid gap-5 sm:grid-cols-2">
    <x-form.select name="bus_id" label="Bus" :options="$busOptions" :value="$schedule?->bus_id" placeholder="Choose a bus" required />
    <x-form.select name="route_id" label="Route" :options="$routeOptions" :value="$schedule?->route_id" placeholder="Choose a route" required />
    <x-form.input name="departure_date" type="date" label="Departure date" :value="$schedule?->departure_date->toDateString()" required :min="$schedule ? null : today()->toDateString()" />
    <x-form.input name="departure_time" type="time" label="Departure time" :value="$schedule ? substr($schedule->departure_time, 0, 5) : null" required />
    @if ($schedule)
        <x-form.select name="status" label="Status" :options="$statuses" :value="$schedule->status" required />
    @endif
</div>
@if ($buses->isEmpty() || $routes->isEmpty())
    <p class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">
        You need at least one active bus and one active route.
        <a href="{{ $buses->isEmpty() ? route('portal.buses.create') : route('portal.routes.create') }}" class="font-semibold underline">Add one now</a>.
    </p>
@endif
