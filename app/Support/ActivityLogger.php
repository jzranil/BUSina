<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Record an action, e.g. ActivityLogger::log('bus.created', 'Added bus BUS006', $bus);
     * The acting user defaults to whoever is logged in.
     */
    public static function log(string $action, string $description, ?Model $subject = null, ?User $user = null): ActivityLog
    {
        $user ??= Auth::user();

        return ActivityLog::create([
            'user_id' => $user?->getKey(),
            'action' => $action,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
        ]);
    }
}
