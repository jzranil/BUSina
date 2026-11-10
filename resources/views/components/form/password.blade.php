{{-- Password field (white background) with a "Show / Hide" text toggle, as in the mockup.
     :toggle="false" hides the toggle (e.g. on "Confirm password"). --}}
@props(['name' => 'password', 'label' => 'Password', 'required' => false, 'hint' => null, 'errorKey' => null, 'toggle' => true])
@php
    $key = $errorKey ?? $name;
    $id = $attributes->get('id', 'f_'.$name);
    $hasError = $errors->has($key);
@endphp
<div {{ $attributes->only('class') }} x-data="{ show: false }">
    <label for="{{ $id }}" class="form-label">
        {{ $label }}@if ($required)<span class="text-red-600" aria-hidden="true"> *</span>@endif
    </label>
    <div class="relative">
        <input id="{{ $id }}" name="{{ $name }}" :type="show ? 'text' : 'password'" type="password"
               @if ($required) aria-required="true" @endif
               @if ($hasError) aria-invalid="true" @endif
               {{ $attributes->except(['class', 'id'])->merge(['class' => 'field bg-white text-slate-900'.($toggle ? ' pr-16' : '').($hasError ? ' field-error' : '')]) }}>
        @if ($toggle)
            <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-xs font-semibold text-brand-800 hover:text-brand-600"
                    :aria-label="show ? 'Hide password' : 'Show password'" x-text="show ? 'Hide' : 'Show'">Show</button>
        @endif
    </div>
    @if ($hint && ! $hasError)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($key)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
