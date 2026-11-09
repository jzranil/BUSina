<?php

namespace Database\Seeders;

use App\Models\BusRoute;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            // name,                 from,     to,          km,  minutes, fare
            ['Manila - Baguio',      'Manila', 'Baguio',    250, 300, 750],
            ['Manila - Batangas',    'Manila', 'Batangas',  110, 150, 280],
            ['Manila - La Union',    'Manila', 'La Union',  270, 360, 720],
            ['Pasay - Legazpi',      'Pasay',  'Legazpi',   550, 600, 1150],
            ['Cubao - Naga',         'Cubao',  'Naga',      380, 480, 900],
            ['Baguio - Manila',      'Baguio', 'Manila',    250, 300, 750],
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
