{{-- Approval queue card (dashboard). Needs $pendingQueue. --}}
<div class="card overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div>
            <h2 class="font-semibold text-slate-900">Pending approval</h2>
            <p class="text-sm text-slate-500">Oldest requests first</p>
        </div>
        <a href="{{ route('portal.reservations.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View all</a>
    </div>
    @forelse ($pendingQueue as $reservation)
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 last:border-0 sm:flex-row sm:items-center">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('portal.reservations.show', $reservation) }}" class="font-mono text-sm font-semibold text-brand-700 hover:underline">{{ $reservation->booking_code }}</a>
                    <span class="text-sm font-medium text-slate-900">{{ $reservation->contact_name }}</span>
                    <span class="text-xs text-slate-400">{{ $reservation->created_at->diffForHumans() }}</span>
                </div>
                <p class="mt-0.5 truncate text-sm text-slate-500">
                    {{ $reservation->schedule->label() }} · Seat {{ $reservation->seats->pluck('seat_number')->implode(', ') }} · ₱{{ number_format($reservation->total_fare, 2) }}
                </p>
            </div>
            <div class="flex gap-2">
                <x-confirm-action :action="route('portal.reservations.approve', $reservation)" method="PATCH" variant="success"
                                  title="Approve {{ $reservation->booking_code }}?" message="The customer will see this booking as Approved." confirm="Approve"
                                  button-class="btn-success btn-sm">
                    <x-icon name="check" class="h-4 w-4" /> Approve
                </x-confirm-action>
                <x-confirm-action :action="route('portal.reservations.reject', $reservation)" method="PATCH" :remarks="true"
                                  title="Reject {{ $reservation->booking_code }}?" message="The seats will be released for other passengers." confirm="Reject">
                    <x-icon name="x" class="h-4 w-4" /> Reject
                </x-confirm-action>
            </div>
        </div>
    @empty
        <x-empty-state icon="check-circle" title="All caught up" message="There are no reservations waiting for approval." />
    @endforelse
</div>
