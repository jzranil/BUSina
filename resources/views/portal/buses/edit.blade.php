<x-app-layout title="Edit Bus">
    <x-page-header :title="'Edit bus '.$bus->bus_number" subtitle="Fields marked * are required." />
    <form method="POST" action="{{ route('portal.buses.update', $bus) }}" class="card max-w-3xl space-y-6 p-6" novalidate>
        @method('PUT')
        @include('portal.buses._form')
        <x-form.actions :cancel="route('portal.buses.index')" submit="Save changes" />
    </form>
</x-app-layout>
