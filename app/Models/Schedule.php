<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * One trip: a bus running a route at a date and time.
 */
class Schedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_id';

    public const STATUSES = [
        'scheduled' => 'Scheduled',
        'departed' => 'Departed',
        'completed' => 'Completed',
        'canceled' => 'Canceled',
    ];

    protected $fillable = [
        'bus_id',
        'route_id',
        'departure_date',
        'departure_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
        ];
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class, 'bus_id', 'bus_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(BusRoute::class, 'route_id', 'route_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'schedule_id', 'schedule_id');
    }

    /** Seats currently taken on this trip (pending or approved bookings). */
    public function heldSeats(): HasMany
    {
        return $this->hasMany(ReservationSeat::class, 'schedule_id', 'schedule_id')->where('is_held', true);
    }

    /** Trips a customer can still book: scheduled, bus active, departure in the future. */
    public function scopeBookable(Builder $query): void
    {
        $query->where('status', 'scheduled')
            ->whereHas('bus', fn (Builder $bus) => $bus->where('status', 'active'))
            ->where(function (Builder $q) {
                $q->whereDate('departure_date', '>', today())
                    ->orWhere(fn (Builder $q) => $q
                        ->whereDate('departure_date', today())
                        ->where('departure_time', '>', now()->format('H:i:s')));
            });
    }

    public function departsAt(): Carbon
    {
        return Carbon::parse($this->departure_date->toDateString().' '.$this->departure_time);
    }

    public function arrivesAt(): Carbon
    {
        return $this->departsAt()->addMinutes($this->route->duration_minutes);
    }

    public function isBookable(): bool
    {
        return $this->status === 'scheduled'
            && $this->bus->status === 'active'
            && $this->departsAt()->isFuture();
    }

    /** @return array<int, int> seat numbers already taken */
    public function takenSeatNumbers(): array
    {
        return $this->heldSeats()->pluck('seat_number')->map(fn ($n) => (int) $n)->all();
    }

    /** Uses held_seats_count when loaded with withCount('heldSeats') to avoid an extra query. */
    public function availableSeats(): int
    {
        $held = $this->held_seats_count ?? $this->heldSeats()->count();

        return max(0, $this->bus->seating_capacity - $held);
    }

    /** "Manila → Baguio, Nov 10 2026 6:00 AM" */
    public function label(): string
    {
        return $this->route->departure_location.' → '.$this->route->destination.', '
            .$this->departsAt()->format('M j Y g:i A');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
