<?php

namespace Database\Factories;

use App\Models\BusRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusRoute>
 */
class BusRouteFactory extends Factory
{
    public function definition(): array
    {
        $from = fake()->unique()->city();
        $to = fake()->unique()->city();

        return [
            'route_name' => "{$from} - {$to}",
            'departure_location' => $from,
            'destination' => $to,
            'distance' => 250,
            'duration_minutes' => 300,
            'fare' => 750,
            'is_active' => true,
        ];
    }
}
