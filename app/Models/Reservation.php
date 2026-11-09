<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One booking by a customer for one trip, covering one or more seats.
 * Create, approve, reject and cancel through App\Services\ReservationService.
 */
class Reservation extends Model
{
    use HasFactory;

    protected $primaryKey = 'reservation_id';

    public const STATUSES = [
        'pending' => 'Pending Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'canceled' => 'Canceled',
    ];

    protected $fillable = [
        'booking_code',
        'schedule_id',
        'user_id',
        'contact_name',
        'contact_number',
        'passenger_count',
        'total_fare',
        'status',
        'reviewed_by',
        'reviewed_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'passenger_count' => 'integer',
            'total_fare' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'user_id');
    }

    public function seats(): HasMany
    {
        return $this->hasMany(ReservationSeat::class, 'reservation_id', 'reservation_id');
    }

    /** Pending or approved: the seats are still held. */
    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'approved'], true);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
