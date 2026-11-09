<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationSeat extends Model
{
    protected $fillable = [
        'reservation_id',
        'schedule_id',
        'seat_number',
        'passenger_name',
        'is_held',
    ];

    protected function casts(): array
    {
        return [
            'seat_number' => 'integer',
            'is_held' => 'boolean',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }
}
