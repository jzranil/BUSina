<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Reservation;
use App\Models\Schedule;

/** Admin and employee dashboard: stats, 7-day booking trend, pending-approval queue. */
class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'active_buses' => Bus::active()->count(),
            'total_buses' => Bus::count(),
            'active_routes' => BusRoute::active()->count(),
            'trips_today' => Schedule::whereDate('departure_date', today())->where('status', '!=', 'canceled')->count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'approved_revenue' => (float) Reservation::where('status', 'approved')->sum('total_fare'),
        ];

        // Reservations made per day over the last 7 days (grouped in PHP so it works on SQLite and MySQL)
        $since = today()->subDays(6);
        $counts = Reservation::where('created_at', '>=', $since)->get(['created_at'])
            ->countBy(fn ($r) => $r->created_at->toDateString());
        $trend = collect(range(0, 6))->mapWithKeys(function ($offset) use ($since, $counts) {
            $day = $since->copy()->addDays($offset)->toDateString();

            return [$day => $counts->get($day, 0)];
        });

        $pendingQueue = Reservation::with(['schedule.route', 'schedule.bus', 'user', 'seats'])
            ->where('status', 'pending')
            ->oldest()
            ->limit(5)
            ->get();

        return view('portal.dashboard', compact('stats', 'trend', 'pendingQueue'));
    }
}
