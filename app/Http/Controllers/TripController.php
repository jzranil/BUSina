<?php

namespace App\Http\Controllers;

use App\Models\BusRoute;
use App\Models\Schedule;
use App\Services\ReservationService;
use Illuminate\Http\Request;

/** Public: search trips and view a trip's seat map. Booking itself requires a customer login. */
class TripController extends Controller
{
    /** Landing page: hero with trip search, popular routes. */
    public function home()
    {
        return view('welcome', [
            'origins' => $this->origins(),
            'destinations' => $this->destinations(),
            'popularRoutes' => BusRoute::active()->withCount('schedules')->orderByDesc('schedules_count')->limit(6)->get(),
            'tripCount' => Schedule::bookable()->count(),
        ]);
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'string', 'max:100'],
            'to' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
            'passengers' => ['nullable', 'integer', 'min:1', 'max:'.ReservationService::MAX_SEATS_PER_BOOKING],
        ], [
            'date.after_or_equal' => 'Choose today or a later date.',
        ]);

        $trips = Schedule::bookable()
            ->with(['bus', 'route'])
            ->withCount('heldSeats')
            ->whereHas('route', function ($q) use ($filters) {
                $q->where('is_active', true)
                    ->when($filters['from'] ?? null, fn ($q, $from) => $q->where('departure_location', $from))
                    ->when($filters['to'] ?? null, fn ($q, $to) => $q->where('destination', $to));
            })
            ->when($filters['date'] ?? null, fn ($q, $date) => $q->whereDate('departure_date', $date))
            // Only trips with enough free seats for the whole group
            ->whereRaw(
                '(select seating_capacity from buses where buses.bus_id = schedules.bus_id)
                 - (select count(*) from reservation_seats where reservation_seats.schedule_id = schedules.schedule_id and is_held = 1) >= ?',
                [(int) ($filters['passengers'] ?? 1)]
            )
            ->orderBy('departure_date')->orderBy('departure_time')
            ->paginate(10)
            ->withQueryString();

        return view('trips.index', [
            'trips' => $trips,
            'filters' => $filters,
            'origins' => $this->origins(),
            'destinations' => $this->destinations(),
        ]);
    }

    private function origins()
    {
        return BusRoute::active()->orderBy('departure_location')->distinct()->pluck('departure_location');
    }

    private function destinations()
    {
        return BusRoute::active()->orderBy('destination')->distinct()->pluck('destination');
    }

    /** Public list of active routes with fare and travel time. */
    public function routes()
    {
        $routes = BusRoute::active()
            ->withCount(['schedules as upcoming_trips_count' => fn ($q) => $q->bookable()])
            ->orderBy('departure_location')->orderBy('destination')
            ->get();

        return view('trips.routes', compact('routes'));
    }

    public function show(Request $request, Schedule $schedule)
    {
        $schedule->load(['bus', 'route']);

        abort_unless($schedule->isBookable(), 404, 'This trip is no longer open for booking.');

        // A guest who logs in from this page comes back here afterwards
        if (! $request->user()) {
            $request->session()->put('url.intended', $request->fullUrl());
        }

        return view('trips.show', [
            'schedule' => $schedule,
            'takenSeats' => $schedule->takenSeatNumbers(),
            'passengers' => min(max((int) $request->query('passengers', 1), 1), ReservationService::MAX_SEATS_PER_BOOKING),
        ]);
    }
}
