@props(['label', 'value', 'icon' => 'dashboard', 'tone' => 'brand', 'hint' => null])
@php
$tones = [
    'brand' => 'bg-brand-50 text-brand-600',
    'amber' => 'bg-amber-50 text-amber-600',
    'emerald' => 'bg-emerald-50 text-emerald-600',
    'sky' => 'bg-sky-50 text-sky-600',
    'violet' => 'bg-violet-50 text-violet-600',
];
@endphp
<div {{ $attributes->merge(['class' => 'card p-5 flex items-start gap-4']) }}>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $tones[$tone] }}">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>
    <div class="min-w-0">
        <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ $value }}</p>
        @if ($hint)
            <p class="mt-0.5 text-xs text-slate-500">{{ $hint }}</p>
        @endif
    </div>
</div>
