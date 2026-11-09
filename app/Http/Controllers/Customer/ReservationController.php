<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Customer side: "My Reservations". */
class ReservationController extends Controller
{
    public function __construct(private ReservationService $reservations) {}

    public function index(Request $request)
    {
        $reservations = $request->user()->reservations()
            ->with(['schedule.route', 'schedule.bus', 'seats'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.reservations.index', [
            'reservations' => $reservations,
            'statuses' => Reservation::STATUSES,
        ]);
    }

    public function store(StoreReservationRequest $request, Schedule $schedule)
    {
        $data = $request->validated();

        $reservation = $this->reservations->create(
            $request->user(),
            $schedule,
            $data['passengers'],
            $data['contact_name'],
            $data['contact_number'],
        );

        return redirect()
            ->route('customer.reservations.show', $reservation)
            ->with('success', "Reservation {$reservation->booking_code} was submitted and is waiting for approval.");
    }

    public function show(Reservation $reservation)
    {
        Gate::authorize('view', $reservation);
        $reservation->load(['schedule.route', 'schedule.bus', 'seats', 'reviewer']);

        return view('customer.reservations.show', compact('reservation'));
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        Gate::authorize('cancel', $reservation);
        $this->reservations->cancel($reservation, $request->user());

        return back()->with('success', "Reservation {$reservation->booking_code} was canceled.");
    }
}
