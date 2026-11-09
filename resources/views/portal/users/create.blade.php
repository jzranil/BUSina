<x-app-layout title="Add User">
    <x-page-header title="Add user" subtitle="Create employee or admin accounts here. Customers can also sign up themselves." />
    <form method="POST" action="{{ route('portal.users.store') }}" class="card max-w-3xl space-y-6 p-6" novalidate>
        @include('portal.users._form', ['user' => null])
        <x-form.actions :cancel="route('portal.users.index')" submit="Create account" />
    </form>
</x-app-layout>
