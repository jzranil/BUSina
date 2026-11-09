{{-- Password field (white background) with a show/hide toggle. --}}
@props(['name' => 'password', 'label' => 'Password', 'required' => false, 'hint' => null, 'errorKey' => null])
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
               {{ $attributes->except(['class', 'id'])->merge(['class' => 'field pr-11 bg-white text-slate-900'.($hasError ? ' field-error' : '')]) }}>
        <button type="button" @click="show = !show"
                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600"
                :aria-label="show ? 'Hide password' : 'Show password'">
            <x-icon name="eye" class="h-5 w-5" x-show="!show" />
            <x-icon name="eye-off" class="h-5 w-5" x-show="show" x-cloak />
        </button>
    </div>
    @if ($hint && ! $hasError)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($key)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
