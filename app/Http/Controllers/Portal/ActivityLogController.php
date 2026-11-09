<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/** Admin only. */
class ActivityLogController extends Controller
{
    public function __invoke(Request $request)
    {
        $logs = ActivityLog::query()
            ->with('user')
            // filter by area, e.g. ?type=reservation matches reservation.created, reservation.approved, ...
            ->when($request->filled('type'), fn ($q) => $q->where('action', 'like', $request->type.'.%'))
            ->when($request->filled('q'), fn ($q) => $q->where('description', 'like', '%'.$request->q.'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('portal.activity-logs.index', compact('logs'));
    }
}
