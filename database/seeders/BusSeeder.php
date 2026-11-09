<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Bus;
use Illuminate\Database\Seeder;


class BusSeeder extends Seeder
{
   
    public function run(): void
    {
        Bus::create([
    'bus_number' => 'BUS001',
    'seating_capacity' => 45,
    'driver_name' => 'Nicos Delos Santos',
    'departure_location' => 'Manila',
    'destination' => 'Baguio',
    'departure_time' => '06:00:00',
    'arrival_time' => '11:00:00',
]);

Bus::create([
    'bus_number' => 'BUS002',
    'seating_capacity' => 40,
    'driver_name' => 'Harold Nepomoceno',
    'departure_location' => 'Manila',
    'destination' => 'Batangas',
    'departure_time' => '07:00:00',
    'arrival_time' => '10:00:00',
]);

Bus::create([
    'bus_number' => 'BUS003',
    'seating_capacity' => 50,
    'driver_name' => 'Dareen Padilla',
    'departure_location' => 'Manila',
    'destination' => 'Baguio',
    'departure_time' => '08:00:00',
    'arrival_time' => '13:00:00',
]);

Bus::create([
    'bus_number' => 'BUS004',
    'seating_capacity' => 35,
    'driver_name' => 'Norvie Villalon',
    'departure_location' => 'Manila',
    'destination' => 'La Union',
    'departure_time' => '09:00:00',
    'arrival_time' => '15:00:00',
]);

Bus::create([
    'bus_number' => 'BUS005',
    'seating_capacity' => 45,
    'driver_name' => 'Fher Ong',
    'departure_location' => 'Pasay',
    'destination' => 'Legazpi',
    'departure_time' => '05:00:00',
    'arrival_time' => '15:00:00',
]);

    }
}
