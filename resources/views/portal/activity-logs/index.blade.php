<x-app-layout title="Activity Logs">
    <x-page-header title="Activity logs" subtitle="Every booking, approval and change made in BUSina." />

    @php
        $types = ['reservation' => 'Reservations', 'bus' => 'Buses', 'route' => 'Routes', 'schedule' => 'Schedules', 'user' => 'Users'];
        $icons = ['reservation' => 'ticket', 'bus' => 'bus', 'route' => 'map', 'schedule' => 'calendar', 'user' => 'users'];
        $tones = [
            'created' => 'bg-sky-50 text-sky-600', 'updated' => 'bg-violet-50 text-violet-600', 'deleted' => 'bg-red-50 text-red-600',
            'approved' => 'bg-emerald-50 text-emerald-600', 'rejected' => 'bg-red-50 text-red-600', 'canceled' => 'bg-slate-100 text-slate-600',
        ];
        $filtered = request()->hasAny(['q', 'type']);
    @endphp

    <div class="card">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search descriptions" class="field pl-9" aria-label="Search logs">
            </div>
            <select name="type" class="field sm:w-48" aria-label="Filter by area">
                <option value="">All areas</option>
                @foreach ($types as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="btn-secondary"><x-icon name="filter" class="h-4 w-4" /> Filter</button>
                @if ($filtered)
                    <a href="{{ route('portal.activity-logs.index') }}" class="btn-ghost">Clear</a>
                @endif
            </div>
        </form>

        @if ($logs->isEmpty())
            <x-empty-state icon="logs" title="No activity yet" />
        @else
            <ul class="divide-y divide-slate-100">
                @foreach ($logs as $log)
                    @php [$area, $verb] = array_pad(explode('.', $log->action, 2), 2, ''); @endphp
                    <li class="flex items-start gap-4 px-5 py-4">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $tones[$verb] ?? 'bg-slate-100 text-slate-600' }}">
                            <x-icon :name="$icons[$area] ?? 'logs'" class="h-5 w-5" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-slate-900">{{ $log->description }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $log->user?->name ?? 'System' }} · <span title="{{ $log->created_at->format('M j, Y g:i:s A') }}">{{ $log->created_at->diffForHumans() }}</span>
                                · <span class="font-mono">{{ $log->action }}</span>
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
            @if ($logs->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $logs->links() }}</div>
            @endif
        @endif
    </div>
</x-app-layout>
