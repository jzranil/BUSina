{{-- BUSina logo (from the mockup): white steering wheel on a pink badge + "BUS" (maroon) "ina" (pink).
     Use :light="true" on dark backgrounds. --}}
@props(['light' => false, 'size' => 'md', 'wordmark' => true])
@php
    $badge = ['sm' => 'h-8 w-8 rounded-lg', 'md' => 'h-11 w-11 rounded-xl', 'lg' => 'h-14 w-14 rounded-2xl'][$size];
    $icon = ['sm' => 'h-5 w-5', 'md' => 'h-6 w-6', 'lg' => 'h-8 w-8'][$size];
    $text = ['sm' => 'text-lg', 'md' => 'text-2xl', 'lg' => 'text-3xl'][$size];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <span class="{{ $badge }} inline-flex shrink-0 items-center justify-center bg-brand-500 text-white shadow-sm">
        <x-steering-wheel class="{{ $icon }}" />
    </span>
    @if ($wordmark)
        <span class="{{ $text }} font-extrabold tracking-tight"><span class="{{ $light ? 'text-white' : 'text-brand-800' }}">BUS</span><span class="text-brand-500">ina</span></span>
    @endif
</span>
