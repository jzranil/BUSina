<x-app-layout title="Dashboard">
    @php
        $user = auth()->user();
        $maxTrend = max(1, $trend->max());
    @endphp

    <x-page-header :title="'Good '.(now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening')).', '.\Illuminate\Support\Str::before($user->name, ' ')"
                   :subtitle="$user->isAdmin() ? 'Here is what is happening across BUSina today.' : 'Reservations waiting for you are shown first.'">
        <a href="{{ route('portal.schedules.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> New trip</a>
    </x-page-header>

    {{-- Employees: approval queue first --}}
    @if (! $user->isAdmin())
        <div class="mb-6">@include('portal._pending-queue')</div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-stat-card label="Pending approval" :value="$stats['pending_reservations']" icon="ticket" tone="amber" />
        <x-stat-card label="Trips today" :value="$stats['trips_today']" icon="calendar" tone="sky" />
        <x-stat-card label="Approved revenue" :value="'₱'.number_format($stats['approved_revenue'], 2)" icon="cash" tone="emerald" />
        <x-stat-card label="Active buses" :value="$stats['active_buses']" :hint="'of '.$stats['total_buses'].' buses in the fleet'" icon="bus" tone="brand" />
        <x-stat-card label="Active routes" :value="$stats['active_routes']" icon="map" tone="violet" />
        <x-stat-card label="Bookings this week" :value="$trend->sum()" hint="Last 7 days" icon="dashboard" tone="brand" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-5">
        {{-- 7-day booking trend --}}
        <div class="card p-5 xl:col-span-2">
            <h2 class="font-semibold text-slate-900">Bookings, last 7 days</h2>
            <p class="text-sm text-slate-500">New reservations per day</p>
            <div class="mt-6 flex h-48 items-end gap-2" role="img" aria-label="Bar chart of reservations per day">
                @foreach ($trend as $day => $count)
                    @php $d = \Illuminate\Support\Carbon::parse($day); @endphp
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <span class="text-xs font-semibold text-slate-600">{{ $count }}</span>
                        <div class="w-full max-w-[2.5rem] rounded-t-lg {{ $d->isToday() ? 'bg-brand-600' : 'bg-brand-200' }}"
                             style="height: {{ max(4, round($count / $maxTrend * 100)) }}%" title="{{ $d->format('M j') }}: {{ $count }}"></div>
                        <span class="text-xs {{ $d->isToday() ? 'font-bold text-brand-700' : 'text-slate-500' }}">{{ $d->format('D') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        @if ($user->isAdmin())
            <div class="xl:col-span-3">@include('portal._pending-queue')</div>
        @else
            <div class="card p-5 xl:col-span-3">
                <h2 class="font-semibold text-slate-900">Quick links</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ([
                        ['portal.reservations.index', 'ticket', 'All reservations'],
                        ['portal.schedules.index', 'calendar', 'Trip schedules'],
                        ['portal.buses.index', 'bus', 'Buses'],
                        ['portal.routes.index', 'map', 'Routes'],
                    ] as [$r, $icon, $label])
                        <a href="{{ route($r) }}" class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 font-medium text-slate-700 hover:border-brand-300 hover:bg-brand-50/50">
                            <x-icon :name="$icon" class="h-5 w-5 text-brand-600" /> {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
