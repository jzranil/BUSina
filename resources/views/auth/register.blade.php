<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h1 class="text-2xl font-bold text-slate-900">Create your account</h1>
        <p class="mt-1 text-sm text-slate-500">Sign up as a passenger to book bus seats online.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5" novalidate>
            @csrf

            <x-form.input name="name" label="Full name" required autofocus autocomplete="name" placeholder="Juan Dela Cruz" />

            <x-form.input name="email" type="email" label="Email address" required autocomplete="username" placeholder="you@example.com" />

            <x-form.password name="password" label="Password" required autocomplete="new-password" hint="At least 8 characters." />

            <x-form.password name="password_confirmation" label="Confirm password" required autocomplete="new-password" />

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <label for="terms" class="flex items-start gap-2 text-sm text-slate-600">
                    <x-checkbox name="terms" id="terms" />
                    <span>I agree to the <a target="_blank" href="{{ route('terms.show') }}" class="font-medium text-brand-600 underline">Terms of Service</a>
                        and <a target="_blank" href="{{ route('policy.show') }}" class="font-medium text-brand-600 underline">Privacy Policy</a><span class="text-red-600"> *</span></span>
                </label>
                @error('terms') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @endif

            <button type="submit" class="btn-primary w-full">Create account</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Log in</a>
        </p>
    </x-authentication-card>
</x-guest-layout>
