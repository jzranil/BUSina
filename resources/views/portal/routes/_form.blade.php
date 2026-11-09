{{-- Shared add/edit form. $busRoute is null when adding. --}}
@csrf
<div class="grid gap-5 sm:grid-cols-2">
    <x-form.input name="route_name" label="Route name" :value="$busRoute?->route_name" required placeholder="Manila - Baguio" class="sm:col-span-2" maxlength="100" />
    <x-form.input name="departure_location" label="Departure location" :value="$busRoute?->departure_location" required placeholder="Manila" maxlength="100" />
    <x-form.input name="destination" label="Destination" :value="$busRoute?->destination" required placeholder="Baguio" maxlength="100" />
    <x-form.input name="distance" type="number" step="0.01" label="Distance (km)" :value="$busRoute ? (float) $busRoute->distance : null" required min="1" />
    <x-form.input name="duration_minutes" type="number" label="Travel time (minutes)" :value="$busRoute?->duration_minutes" required min="15" max="1440" hint="Used to compute the arrival time, e.g. 300 = 5 hours." />
    <x-form.input name="fare" type="number" step="0.01" label="Fare per seat (₱)" :value="$busRoute ? (float) $busRoute->fare : null" required min="1" />
    <div class="flex items-center pt-6">
        <input type="hidden" name="is_active" value="0">
        <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
            <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                   @checked(old('is_active', $busRoute?->is_active ?? true))>
            Active (customers can book trips on this route)
        </label>
    </div>
</div>
