<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h1 class="text-2xl font-bold text-slate-900">Choose a new password</h1>

        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <x-form.input name="email" type="email" label="Email address" :value="$request->email" required autofocus autocomplete="username" />
            <x-form.password name="password" label="New password" required autocomplete="new-password" hint="At least 8 characters." />
            <x-form.password name="password_confirmation" label="Confirm new password" required autocomplete="new-password" />

            <button type="submit" class="btn-primary w-full">Reset password</button>
        </form>
    </x-authentication-card>
</x-guest-layout>
