{{-- Login / register / password pages: brand panel on the left (desktop), form on the right. --}}
<div class="flex min-h-screen bg-slate-50">
    <div class="relative hidden w-1/2 overflow-hidden bg-gradient-to-br from-brand-600 via-brand-700 to-slate-900 lg:flex lg:flex-col lg:justify-between lg:p-12">
        <a href="{{ route('home') }}" aria-label="BUSina home"><x-logo light size="lg" /></a>

        <div class="relative z-10 max-w-md text-white">
            <h2 class="text-4xl font-extrabold leading-tight">Your seat is just a few taps away.</h2>
            <p class="mt-4 text-lg text-brand-100">Search trips, pick your exact seat on the bus map, and track your reservation from booking to boarding.</p>
            <ul class="mt-8 space-y-3 text-brand-50">
                @foreach (['Real-time seat availability', 'Choose your own seat', 'Booking updates when staff approve'] as $point)
                    <li class="flex items-center gap-3">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/15"><x-icon name="check" class="h-4 w-4" /></span>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="relative z-10 text-sm text-brand-200">&copy; {{ date('Y') }} BUSina</p>

        {{-- decorative steering wheel --}}
        <x-steering-wheel class="pointer-events-none absolute -bottom-24 -right-24 h-96 w-96 text-white/10" stroke-width="1.25" />
    </div>

    <div class="flex w-full flex-col items-center justify-center px-4 py-10 sm:px-6 lg:w-1/2">
        <div class="mb-8 lg:hidden">{{ $logo }}</div>
        <div class="w-full max-w-md">
            <div class="card p-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
