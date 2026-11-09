<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Staff side: review every customer's reservations. */
class ReservationController extends Controller
{
    public function __construct(private ReservationService $reservations) {}

    public function index(Request $request)
    {
        $reservations = Reservation::query()
            ->with(['schedule.route', 'schedule.bus', 'user', 'seats'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('schedule_id'), fn ($q) => $q->where('schedule_id', $request->schedule_id))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->q.'%';
                $query->where(fn ($q) => $q
                    ->where('booking_code', 'like', $term)
                    ->orWhere('contact_name', 'like', $term)
                    ->orWhere('contact_number', 'like', $term));
            })
            // Pending first (oldest first, so nobody waits too long), then everything else newest first
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN status = 'pending' THEN created_at END ASC")
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('portal.reservations.index', [
            'reservations' => $reservations,
            'statuses' => Reservation::STATUSES,
        ]);
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['schedule.route', 'schedule.bus', 'user', 'seats', 'reviewer']);

        return view('portal.reservations.show', compact('reservation'));
    }

    public function approve(Request $request, Reservation $reservation)
    {
        Gate::authorize('review', $reservation);
        $this->reservations->approve($reservation, $request->user());

        return back()->with('success', "Reservation {$reservation->booking_code} was approved.");
    }

    public function reject(Request $request, Reservation $reservation)
    {
        Gate::authorize('review', $reservation);
        $data = $request->validate(['remarks' => ['nullable', 'string', 'max:255']]);
        $this->reservations->reject($reservation, $request->user(), $data['remarks'] ?? null);

        return back()->with('success', "Reservation {$reservation->booking_code} was rejected and its seats were released.");
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        Gate::authorize('cancel', $reservation);
        $this->reservations->cancel($reservation, $request->user());

        return back()->with('success', "Reservation {$reservation->booking_code} was canceled and its seats were released.");
    }
}
