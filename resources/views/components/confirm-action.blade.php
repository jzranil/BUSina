{{--
    A button that asks "Are you sure?" in a dialog before submitting.

    <x-confirm-action :action="route('portal.buses.destroy', $bus)" method="DELETE"
        title="Delete bus?" message="This cannot be undone." confirm="Delete">
        <x-icon name="trash" class="h-4 w-4" /> Delete
    </x-confirm-action>

    :remarks="true" adds an optional remarks box (used when rejecting reservations).
--}}
@props([
    'action',
    'method' => 'POST',
    'title' => 'Are you sure?',
    'message' => null,
    'confirm' => 'Confirm',
    'variant' => 'danger',
    'buttonClass' => 'btn-secondary btn-sm',
    'remarks' => false,
])
<div x-data="{ open: false }" class="inline-block" @keydown.escape.window="open = false">
    <button type="button" @click="open = true" {{ $attributes->merge(['class' => $buttonClass]) }}>
        {{ $slot }}
    </button>

    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true">
            <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-900/50" @click="open = false"></div>
            <form x-show="open" x-transition method="POST" action="{{ $action }}" class="card relative w-full max-w-md p-6 text-left whitespace-normal">
                @csrf
                @if (strtoupper($method) !== 'POST')
                    @method($method)
                @endif
                <div class="flex items-start gap-4">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $variant === 'danger' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }}">
                        <x-icon :name="$variant === 'danger' ? 'alert' : 'check'" class="h-5 w-5" />
                    </span>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                        @if ($message)
                            <p class="mt-1 text-sm text-slate-600">{{ $message }}</p>
                        @endif
                        @if ($remarks)
                            <label class="form-label mt-4" for="remarks-{{ md5($action) }}">Remarks (optional)</label>
                            <textarea id="remarks-{{ md5($action) }}" name="remarks" rows="2" maxlength="255" class="field" placeholder="Reason shown to the customer"></textarea>
                        @endif
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn-secondary" @click="open = false">Cancel</button>
                    <button type="submit" class="{{ $variant === 'danger' ? 'btn-danger' : 'btn-success' }}">{{ $confirm }}</button>
                </div>
            </form>
        </div>
    </template>
</div>
