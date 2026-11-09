{{-- BUSina logo: steering wheel on a rose badge + wordmark. Use :light="true" on dark backgrounds. --}}
@props(['light' => false, 'size' => 'md', 'wordmark' => true])
@php
    $badge = ['sm' => 'h-8 w-8', 'md' => 'h-10 w-10', 'lg' => 'h-14 w-14'][$size];
    $icon = ['sm' => 'h-5 w-5', 'md' => 'h-6 w-6', 'lg' => 'h-9 w-9'][$size];
    $text = ['sm' => 'text-lg', 'md' => 'text-xl', 'lg' => 'text-3xl'][$size];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <span class="{{ $badge }} inline-flex shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm ring-1 ring-brand-700/20">
        <x-steering-wheel class="{{ $icon }}" />
    </span>
    @if ($wordmark)
        <span class="{{ $text }} font-extrabold tracking-tight {{ $light ? 'text-white' : 'text-slate-900' }}">BUS<span class="{{ $light ? 'text-brand-300' : 'text-brand-600' }}">ina</span></span>
    @endif
</span>
