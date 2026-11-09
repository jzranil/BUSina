<x-app-layout title="Edit User">
    <x-page-header :title="'Edit '.$user->name" subtitle="Fields marked * are required." />
    <form method="POST" action="{{ route('portal.users.update', $user) }}" class="card max-w-3xl space-y-6 p-6" novalidate>
        @method('PUT')
        @include('portal.users._form')
        <x-form.actions :cancel="route('portal.users.index')" submit="Save changes" />
    </form>
</x-app-layout>
