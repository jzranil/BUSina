{{-- Status pill. Usage: <x-badge :status="$reservation->status">{{ $reservation->statusLabel() }}</x-badge> --}}
@props(['status' => 'default'])
@php
$colors = [
    'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
    'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    'scheduled' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
    'departed' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
    'completed' => 'bg-slate-100 text-slate-700 ring-slate-500/20',
    'rejected' => 'bg-red-50 text-red-700 ring-red-600/20',
    'canceled' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
    'inactive' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
    'maintenance' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
    'admin' => 'bg-brand-50 text-brand-700 ring-brand-600/20',
    'employee' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
    'customer' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
    'default' => 'bg-slate-100 text-slate-700 ring-slate-500/20',
];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset whitespace-nowrap '.($colors[$status] ?? $colors['default'])]) }}>
    {{ $slot }}
</span>
