<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BusRoute;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $upcoming = $user->reservations()
            ->with(['schedule.route', 'schedule.bus', 'seats'])
            ->whereIn('status', ['pending', 'approved'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('departure_date', '>=', today()))
            ->get()
            ->sortBy(fn ($r) => $r->schedule->departsAt())
            ->values();

        $counts = $user->reservations()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('customer.dashboard', [
            'upcoming' => $upcoming,
            'counts' => $counts,
            'popularRoutes' => BusRoute::active()->withCount('schedules')->orderByDesc('schedules_count')->limit(4)->get(),
        ]);
    }
}
