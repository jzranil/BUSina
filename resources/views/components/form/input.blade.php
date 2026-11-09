{{--
    Label + input + server-side error, with old() input kept after a failed submit.
    <x-form.input name="bus_number" label="Bus number" :value="$bus->bus_number ?? null" required />
--}}
@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])
@php
    $key = preg_replace('/\[(.*?)\]/', '.$1', $name);           // passengers[0][name] -> passengers.0.name
    $id = $attributes->get('id', 'f_'.str_replace('.', '_', $key));
    $hasError = $errors->has($key);
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}@if ($required)<span class="text-red-600" aria-hidden="true"> *</span>@endif
        </label>
    @endif
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
           @if ($type !== 'password') value="{{ old($key, $value) }}" @endif
           @if ($required) aria-required="true" @endif
           @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}_error" @endif
           {{ $attributes->except(['class', 'id'])->merge(['class' => 'field'.($hasError ? ' field-error' : '')]) }}>
    @if ($hint && ! $hasError)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($key)
        <p id="{{ $id }}_error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
