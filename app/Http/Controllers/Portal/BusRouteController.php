<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusRouteRequest;
use App\Models\BusRoute;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class BusRouteController extends Controller
{
    public function index(Request $request)
    {
        $routes = BusRoute::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->q.'%';
                $query->where(fn ($q) => $q
                    ->where('route_name', 'like', $term)
                    ->orWhere('departure_location', 'like', $term)
                    ->orWhere('destination', 'like', $term));
            })
            ->when($request->filled('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->withCount('schedules')
            ->orderBy('departure_location')->orderBy('destination')
            ->paginate(10)
            ->withQueryString();

        return view('portal.routes.index', compact('routes'));
    }

    public function create()
    {
        return view('portal.routes.create');
    }

    public function store(BusRouteRequest $request)
    {
        $busRoute = BusRoute::create($request->validated());
        ActivityLogger::log('route.created', "Added route {$busRoute->departure_location} → {$busRoute->destination}", $busRoute);

        return redirect()->route('portal.routes.index')->with('success', "Route {$busRoute->route_name} was added.");
    }

    public function show(BusRoute $busRoute)
    {
        $busRoute->load(['schedules' => fn ($q) => $q->with('bus')->withCount('heldSeats')
            ->whereDate('departure_date', '>=', today())
            ->orderBy('departure_date')->orderBy('departure_time')
            ->limit(10)]);

        return view('portal.routes.show', compact('busRoute'));
    }

    public function edit(BusRoute $busRoute)
    {
        return view('portal.routes.edit', compact('busRoute'));
    }

    public function update(BusRouteRequest $request, BusRoute $busRoute)
    {
        $busRoute->update($request->validated());
        ActivityLogger::log('route.updated', "Updated route {$busRoute->route_name}", $busRoute);

        return redirect()->route('portal.routes.index')->with('success', "Route {$busRoute->route_name} was updated.");
    }

    public function destroy(BusRoute $busRoute)
    {
        if ($busRoute->schedules()->exists()) {
            return back()->with('error', "Route {$busRoute->route_name} has trips. Mark it inactive instead of deleting it.");
        }

        $busRoute->delete();
        ActivityLogger::log('route.deleted', "Deleted route {$busRoute->route_name}");

        return redirect()->route('portal.routes.index')->with('success', "Route {$busRoute->route_name} was deleted.");
    }
}
