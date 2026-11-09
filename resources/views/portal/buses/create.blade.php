<x-app-layout title="Add Bus">
    <x-page-header title="Add bus" subtitle="Fields marked * are required." />
    <form method="POST" action="{{ route('portal.buses.store') }}" class="card max-w-3xl space-y-6 p-6" novalidate>
        @include('portal.buses._form', ['bus' => null])
        <x-form.actions :cancel="route('portal.buses.index')" submit="Add bus" />
    </form>
</x-app-layout>
