<x-app-layout title="Add Route">
    <x-page-header title="Add route" subtitle="Fields marked * are required." />
    <form method="POST" action="{{ route('portal.routes.store') }}" class="card max-w-3xl space-y-6 p-6" novalidate>
        @include('portal.routes._form', ['busRoute' => null])
        <x-form.actions :cancel="route('portal.routes.index')" submit="Add route" />
    </form>
</x-app-layout>
