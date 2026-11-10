<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

/**
 * One trip per day for the next 7 days for each active bus, plus a completed trip from yesterday.
 * Each bus keeps to one route at one time of day, so no bus is double-booked.
 */
class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $plan = [
            // bus,     route,               time
            ['BUS001', 'Manila – Baguio',   '06:00:00'],
            ['BUS002', 'Manila – Batangas', '07:00:00'],
            ['BUS003', 'Manila – Baler',    '08:00:00'],
            ['BUS004', 'Manila – Naga',     '20:00:00'],
            ['BUS005', 'Manila – Legazpi',  '05:00:00'],
            ['BUS007', 'Cubao – Vigan',     '21:00:00'],
        ];

        foreach ($plan as [$busNumber, $routeName, $time]) {
            $bus = Bus::where('bus_number', $busNumber)->firstOrFail();
            $route = BusRoute::where('route_name', $routeName)->firstOrFail();

            Schedule::create([
                'bus_id' => $bus->bus_id,
                'route_id' => $route->route_id,
                'departure_date' => today()->subDay()->toDateString(),
                'departure_time' => $time,
                'status' => 'completed',
            ]);

            foreach (range(1, 7) as $day) {
                Schedule::create([
                    'bus_id' => $bus->bus_id,
                    'route_id' => $route->route_id,
                    'departure_date' => today()->addDays($day)->toDateString(),
                    'departure_time' => $time,
                    'status' => 'scheduled',
                ]);
            }
        }
    }
}
