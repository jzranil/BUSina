{{-- Success / error messages from redirects ->with('success') / ->with('error') --}}
@foreach (['success' => ['bg-emerald-50 border-emerald-200 text-emerald-800', 'check-circle'], 'error' => ['bg-red-50 border-red-200 text-red-800', 'alert']] as $key => [$classes, $icon])
    @if (session($key))
        <div x-data="{ show: true }" x-show="show" x-transition role="{{ $key === 'error' ? 'alert' : 'status' }}"
             class="mb-6 flex items-start gap-3 rounded-xl border px-4 py-3 text-sm {{ $classes }}">
            <x-icon :name="$icon" class="h-5 w-5 shrink-0" />
            <p class="flex-1">{{ session($key) }}</p>
            <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Dismiss">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>
    @endif
@endforeach

{{-- Errors that don't belong to one field (e.g. "seat was just taken", "only pending can be approved") --}}
@foreach (['status', 'schedule', 'passengers'] as $key)
    @error($key)
        <div role="alert" class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <x-icon name="alert" class="h-5 w-5 shrink-0" />
            <p>{{ $message }}</p>
        </div>
    @enderror
@endforeach
