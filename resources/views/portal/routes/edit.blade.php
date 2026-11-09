<x-app-layout title="Edit Route">
    <x-page-header :title="'Edit route '.$busRoute->route_name" subtitle="Fields marked * are required." />
    <form method="POST" action="{{ route('portal.routes.update', $busRoute) }}" class="card max-w-3xl space-y-6 p-6" novalidate>
        @method('PUT')
        @include('portal.routes._form')
        <x-form.actions :cancel="route('portal.routes.index')" submit="Save changes" />
    </form>
</x-app-layout>
