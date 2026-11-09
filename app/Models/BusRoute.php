<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A travel route (e.g. Manila → Baguio).
 *
 * Named BusRoute instead of Route so it does not clash with Laravel's Route facade.
 */
class BusRoute extends Model
{
    use HasFactory;

    protected $table = 'routes';

    protected $primaryKey = 'route_id';

    protected $fillable = [
        'route_name',
        'departure_location',
        'destination',
        'distance',
        'duration_minutes',
        'fare',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'distance' => 'decimal:2',
            'duration_minutes' => 'integer',
            'fare' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'route_id', 'route_id');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** "5h 30m" */
    public function durationLabel(): string
    {
        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        return trim(($hours ? "{$hours}h " : '').($minutes ? "{$minutes}m" : ''));
    }
}
