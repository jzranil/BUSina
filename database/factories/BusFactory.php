<?php

namespace Database\Factories;

use App\Models\Bus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bus>
 */
class BusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'bus_number' => 'BUS'.fake()->unique()->numerify('###'),
            'plate_number' => strtoupper(fake()->unique()->bothify('???')).' '.fake()->numerify('####'),
            'bus_type' => fake()->randomElement(['ordinary', 'aircon', 'deluxe']),
            'seating_capacity' => 45,
            'driver_name' => fake()->firstName().' '.fake()->lastName(),
            'status' => 'active',
        ];
    }
}
