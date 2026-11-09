<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h1 class="text-2xl font-bold text-slate-900">Welcome back</h1>
        <p class="mt-1 text-sm text-slate-500">Log in to manage your trips and reservations.</p>

        @session('status')
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ $value }}</div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5" novalidate>
            @csrf

            <x-form.input name="email" type="email" label="Email address" required autofocus autocomplete="username" placeholder="you@example.com" />

            <x-form.password name="password" label="Password" required autocomplete="current-password" />

            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center gap-2 text-sm text-slate-600">
                    <x-checkbox id="remember_me" name="remember" />
                    Remember me
                </label>
                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-brand-600 hover:text-brand-700" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn-primary w-full">Log in</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            New to BUSina?
            <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-700">Create an account</a>
        </p>
    </x-authentication-card>
</x-guest-layout>
