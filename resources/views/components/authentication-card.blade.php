{{-- Login / register / password pages: a centered white card on the pale pink background (group mockup). --}}
<div class="flex min-h-screen items-start justify-center bg-blush px-4 py-6 sm:items-center sm:py-12">
    <div class="w-full max-w-[34rem] rounded-3xl bg-white px-6 py-8 shadow-xl shadow-brand-900/10 sm:px-9 sm:py-9">
        <div class="mb-6 flex justify-center">{{ $logo }}</div>
        {{ $slot }}
    </div>
</div>
