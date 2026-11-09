<x-app-layout title="Edit Trip">
    <x-page-header title="Edit trip" :subtitle="$schedule->label()" />
    <form method="POST" action="{{ route('portal.schedules.update', $schedule) }}" class="card max-w-3xl space-y-6 p-6" novalidate>
        @method('PUT')
        @include('portal.schedules._form')
        <x-form.actions :cancel="route('portal.schedules.index')" submit="Save changes" />
    </form>
</x-app-layout>
