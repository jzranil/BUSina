<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusRequest;
use App\Models\Bus;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function index(Request $request)
    {
        $buses = Bus::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->q.'%';
                $query->where(fn ($q) => $q
                    ->where('bus_number', 'like', $term)
                    ->orWhere('plate_number', 'like', $term)
                    ->orWhere('driver_name', 'like', $term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn ($q) => $q->where('bus_type', $request->type))
            ->withCount('schedules')
            ->orderBy('bus_number')
            ->paginate(10)
            ->withQueryString();

        return view('portal.buses.index', [
            'buses' => $buses,
            'types' => Bus::TYPES,
            'statuses' => Bus::STATUSES,
        ]);
    }

    public function create()
    {
        return view('portal.buses.create', ['types' => Bus::TYPES, 'statuses' => Bus::STATUSES]);
    }

    public function store(BusRequest $request)
    {
        $bus = Bus::create($request->validated());
        ActivityLogger::log('bus.created', "Added bus {$bus->bus_number} ({$bus->plate_number})", $bus);

        return redirect()->route('portal.buses.index')->with('success', "Bus {$bus->bus_number} was added.");
    }

    public function show(Bus $bus)
    {
        $bus->load(['schedules' => fn ($q) => $q->with('route')->withCount('heldSeats')
            ->whereDate('departure_date', '>=', today())
            ->orderBy('departure_date')->orderBy('departure_time')
            ->limit(10)]);

        return view('portal.buses.show', compact('bus'));
    }

    public function edit(Bus $bus)
    {
        return view('portal.buses.edit', ['bus' => $bus, 'types' => Bus::TYPES, 'statuses' => Bus::STATUSES]);
    }

    public function update(BusRequest $request, Bus $bus)
    {
        $bus->update($request->validated());
        ActivityLogger::log('bus.updated', "Updated bus {$bus->bus_number}", $bus);

        return redirect()->route('portal.buses.index')->with('success', "Bus {$bus->bus_number} was updated.");
    }

    public function destroy(Bus $bus)
    {
        if ($bus->schedules()->exists()) {
            return back()->with('error', "Bus {$bus->bus_number} has trips assigned. Set it to Inactive instead of deleting it.");
        }

        $bus->delete();
        ActivityLogger::log('bus.deleted', "Deleted bus {$bus->bus_number}");

        return redirect()->route('portal.buses.index')->with('success', "Bus {$bus->bus_number} was deleted.");
    }
}
