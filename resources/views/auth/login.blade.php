<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h1 class="text-center text-2xl font-bold text-slate-900">Welcome back</h1>

        @session('status')
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ $value }}</div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5" novalidate>
            @csrf

            <x-form.input name="email" type="email" label="Email address" required autofocus autocomplete="username" class="[&_input]:py-3" />

            <x-form.password name="password" label="Password" required autocomplete="current-password" class="[&_input]:py-3" />

            <div class="flex items-center justify-between gap-4">
                <label for="remember_me" class="flex items-center gap-2.5 text-[15px] text-slate-700">
                    <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-slate-700 focus:ring-brand-500">
                    Remember me
                </label>
                @if (Route::has('password.request'))
                    <a class="text-[15px] font-semibold text-brand-800 hover:text-brand-600" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn-primary w-full py-3.5 text-lg font-bold">Login</button>
        </form>

        <p class="mt-6 text-center text-[15px] text-slate-700">
            No account?
            <a href="{{ route('register') }}" class="font-semibold text-brand-800 hover:text-brand-600">Register</a>
        </p>
    </x-authentication-card>
</x-guest-layout>
