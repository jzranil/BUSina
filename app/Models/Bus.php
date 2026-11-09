<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    use HasFactory;

    protected $primaryKey = 'bus_id';

    public const TYPES = [
        'ordinary' => 'Ordinary',
        'aircon' => 'Air-conditioned',
        'deluxe' => 'Deluxe',
    ];

    public const STATUSES = [
        'active' => 'Active',
        'maintenance' => 'Under maintenance',
        'inactive' => 'Inactive',
    ];

    protected $fillable = [
        'bus_number',
        'plate_number',
        'bus_type',
        'seating_capacity',
        'driver_name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'seating_capacity' => 'integer',
        ];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'bus_id', 'bus_id');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->bus_type] ?? $this->bus_type;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
