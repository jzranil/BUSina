<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'bus_id' => Bus::factory(),
            'route_id' => BusRoute::factory(),
            'departure_date' => today()->addDays(3)->toDateString(),
            'departure_time' => '08:00:00',
            'status' => 'scheduled',
        ];
    }
}
