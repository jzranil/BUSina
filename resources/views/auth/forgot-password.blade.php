<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h1 class="text-2xl font-bold text-slate-900">Forgot your password?</h1>
        <p class="mt-1 text-sm text-slate-500">Enter your email and we'll send you a link to choose a new one.</p>

        @session('status')
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ $value }}</div>
        @endsession

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5" novalidate>
            @csrf
            <x-form.input name="email" type="email" label="Email address" required autofocus autocomplete="username" />
            <button type="submit" class="btn-primary w-full">Email reset link</button>
        </form>

        <p class="mt-6 text-center text-sm">
            <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Back to log in</a>
        </p>
    </x-authentication-card>
</x-guest-layout>
