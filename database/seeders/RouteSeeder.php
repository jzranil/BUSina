<?php

namespace Database\Seeders;

use App\Models\BusRoute;
use Illuminate\Database\Seeder;

/** The routes shown on the home page mockup, plus a return trip. */
class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            // name,                 from,     to,         km,  minutes, fare
            ['Manila – Baguio',     'Manila', 'Baguio',   250, 360, 750],
            ['Manila – Legazpi',    'Manila', 'Legazpi',  520, 660, 1150],
            ['Manila – Batangas',   'Manila', 'Batangas', 110, 150, 280],
            ['Cubao – Vigan',       'Cubao',  'Vigan',    400, 510, 950],
            ['Manila – Baler',      'Manila', 'Baler',    230, 360, 650],
            ['Manila – Naga',       'Manila', 'Naga',     450, 480, 900],
            ['Baguio – Manila',     'Baguio', 'Manila',   250, 360, 750],
        ];

        foreach ($routes as [$name, $from, $to, $km, $minutes, $fare]) {
            BusRoute::create([
                'route_name' => $name,
                'departure_location' => $from,
                'destination' => $to,
                'distance' => $km,
                'duration_minutes' => $minutes,
                'fare' => $fare,
                'is_active' => true,
            ]);
        }
    }
}
