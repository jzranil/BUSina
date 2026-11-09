{{-- Shared add/edit form. $bus is null when adding. --}}
@csrf
<div class="grid gap-5 sm:grid-cols-2">
    <x-form.input name="bus_number" label="Bus number" :value="$bus?->bus_number" required placeholder="BUS007" maxlength="20" />
    <x-form.input name="plate_number" label="Plate number" :value="$bus?->plate_number" required placeholder="ABC 1234" maxlength="10" />
    <x-form.select name="bus_type" label="Bus type" :options="$types" :value="$bus?->bus_type" placeholder="Choose a type" required />
    <x-form.input name="seating_capacity" type="number" label="Seating capacity" :value="$bus?->seating_capacity" required min="10" max="60" hint="Between 10 and 60 seats." />
    <x-form.input name="driver_name" label="Driver name" :value="$bus?->driver_name" required maxlength="100" />
    <x-form.select name="status" label="Status" :options="$statuses" :value="$bus?->status ?? 'active'" required />
</div>
