<?php

namespace Database\Seeders;

use App\Models\Bus;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    public function run(): void
    {
        $buses = [
            ['BUS001', 'NBC 1045', 'deluxe',   45, 'Nicos Delos Santos', 'active'],
            ['BUS002', 'TXA 2210', 'aircon',   40, 'Harold Nepomoceno',  'active'],
            ['BUS003', 'UVE 3381', 'aircon',   50, 'Dareen Padilla',     'active'],
            ['BUS004', 'NAQ 4127', 'ordinary', 35, 'Norvie Villalon',    'active'],
            ['BUS005', 'DAB 5562', 'deluxe',   45, 'Fher Ong',           'active'],
            ['BUS006', 'WKR 6093', 'aircon',   45, 'Rommel Bautista',    'maintenance'],
            ['BUS007', 'PXL 7714', 'aircon',   45, 'Ramon Aquino',       'active'],
        ];

        foreach ($buses as [$number, $plate, $type, $capacity, $driver, $status]) {
            Bus::create([
                'bus_number' => $number,
                'plate_number' => $plate,
                'bus_type' => $type,
                'seating_capacity' => $capacity,
                'driver_name' => $driver,
                'status' => $status,
            ]);
        }
    }
}
