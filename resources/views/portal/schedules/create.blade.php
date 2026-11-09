<x-app-layout title="New Trip">
    <x-page-header title="Schedule a trip" subtitle="The arrival time is worked out from the route's travel time. A bus cannot be on two trips at once." />
    <form method="POST" action="{{ route('portal.schedules.store') }}" class="card max-w-3xl space-y-6 p-6" novalidate>
        @include('portal.schedules._form', ['schedule' => null])
        <x-form.actions :cancel="route('portal.schedules.index')" submit="Schedule trip" />
    </form>
</x-app-layout>
