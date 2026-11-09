{{--
    <x-form.select name="bus_type" label="Bus type" :options="$types" :value="$bus->bus_type ?? null" placeholder="Choose a type" required />
    $options is [value => label].
--}}
@props(['name', 'label' => null, 'options' => [], 'value' => null, 'required' => false, 'placeholder' => null])
@php
    $key = preg_replace('/\[(.*?)\]/', '.$1', $name);
    $id = $attributes->get('id', 'f_'.str_replace('.', '_', $key));
    $selected = (string) old($key, $value);
    $hasError = $errors->has($key);
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}@if ($required)<span class="text-red-600" aria-hidden="true"> *</span>@endif
        </label>
    @endif
    <select id="{{ $id }}" name="{{ $name }}"
            @if ($required) aria-required="true" @endif
            @if ($hasError) aria-invalid="true" @endif
            {{ $attributes->except(['class', 'id'])->merge(['class' => 'field'.($hasError ? ' field-error' : '')]) }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($key)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
