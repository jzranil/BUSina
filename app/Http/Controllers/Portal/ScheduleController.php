<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleRequest;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Schedule;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = Schedule::query()
            ->with(['bus', 'route'])
            ->withCount('heldSeats')
            ->when($request->filled('date'), fn ($q) => $q->whereDate('departure_date', $request->date))
            ->when($request->filled('route_id'), fn ($q) => $q->where('route_id', $request->route_id))
            ->when($request->filled('bus_id'), fn ($q) => $q->where('bus_id', $request->bus_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('departure_date')->orderBy('departure_time')
            ->paginate(10)
            ->withQueryString();

        return view('portal.schedules.index', [
            'schedules' => $schedules,
            'buses' => Bus::orderBy('bus_number')->get(['bus_id', 'bus_number']),
            'routes' => BusRoute::orderBy('route_name')->get(['route_id', 'route_name']),
            'statuses' => Schedule::STATUSES,
        ]);
    }

    public function create()
    {
        return view('portal.schedules.create', $this->formOptions());
    }

    public function store(ScheduleRequest $request)
    {
        $schedule = Schedule::create($request->scheduleData() + ['status' => 'scheduled']);
        $schedule->load('route');
        ActivityLogger::log('schedule.created', "Scheduled trip {$schedule->label()}", $schedule);

        return redirect()->route('portal.schedules.index')->with('success', 'Trip scheduled: '.$schedule->label());
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['bus', 'route', 'reservations' => fn ($q) => $q->with('seats', 'user')->latest()]);

        return view('portal.schedules.show', [
            'schedule' => $schedule,
            'takenSeats' => $schedule->takenSeatNumbers(),
        ]);
    }

    public function edit(Schedule $schedule)
    {
        return view('portal.schedules.edit', ['schedule' => $schedule] + $this->formOptions());
    }

    public function update(ScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->scheduleData());
        $schedule->load('route');
        ActivityLogger::log('schedule.updated', "Updated trip {$schedule->label()}", $schedule);

        return redirect()->route('portal.schedules.index')->with('success', 'Trip updated: '.$schedule->label());
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->load('route');

        if ($schedule->reservations()->exists()) {
            return back()->with('error', 'This trip already has reservations. Set its status to Canceled instead of deleting it.');
        }

        $label = $schedule->label();
        $schedule->delete();
        ActivityLogger::log('schedule.deleted', "Deleted trip {$label}");

        return redirect()->route('portal.schedules.index')->with('success', "Trip {$label} was deleted.");
    }

    /** Dropdown options: only active buses and routes can be picked. */
    private function formOptions(): array
    {
        return [
            'buses' => Bus::active()->orderBy('bus_number')->get(),
            'routes' => BusRoute::active()->orderBy('route_name')->get(),
            'statuses' => Schedule::STATUSES,
        ];
    }
}
