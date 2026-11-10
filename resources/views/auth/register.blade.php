<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h1 class="text-center text-2xl font-bold text-slate-900">Create your account</h1>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4" novalidate>
            @csrf

            <x-form.input name="name" label="Full name" required autofocus autocomplete="name" maxlength="255" />

            <x-form.input name="email" type="email" label="Email address" required autocomplete="username" />

            <x-form.input name="contact_number" type="tel" label="Contact number" required inputmode="tel" autocomplete="tel" placeholder="09171234567" />

            <x-form.password name="password" label="Password" required autocomplete="new-password" />

            <x-form.password name="password_confirmation" label="Confirm password" required autocomplete="new-password" :toggle="false"
                             hint="At least 8 characters with a number." />

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <label for="terms" class="flex items-start gap-2 text-sm text-slate-600">
                    <x-checkbox name="terms" id="terms" />
                    <span>I agree to the <a target="_blank" href="{{ route('terms.show') }}" class="font-medium text-brand-800 underline">Terms of Service</a>
                        and <a target="_blank" href="{{ route('policy.show') }}" class="font-medium text-brand-800 underline">Privacy Policy</a><span class="text-red-600"> *</span></span>
                </label>
                @error('terms') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @endif

            <button type="submit" class="btn-primary w-full py-3 text-base font-bold">Register</button>
        </form>

        <p class="mt-5 text-center text-sm text-slate-700">
            Have an account?
            <a href="{{ route('login') }}" class="font-semibold text-brand-800 hover:text-brand-600">Login</a>
        </p>
    </x-authentication-card>
</x-guest-layout>
