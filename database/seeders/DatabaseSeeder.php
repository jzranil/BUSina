<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Demo accounts (one per role) are listed in README.md under "Demo accounts".
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'BUSina Admin',
            'email' => 'admin@busina.test',
            'password' => 'admin1234',
        ]);

        User::factory()->employee()->create([
            'name' => 'BUSina Employee',
            'email' => 'employee@busina.test',
            'password' => 'employee1234',
        ]);

        User::factory()->customer()->create([
            'name' => 'Juan Dela Cruz',
            'email' => 'customer@busina.test',
            'contact_number' => '09171234567',
            'password' => 'customer1234',
        ]);

        User::factory()->customer()->create([
            'name' => 'Maria Santos',
            'email' => 'maria@busina.test',
            'contact_number' => '+639281234567',
            'password' => 'customer1234',
        ]);

        $this->call([
            BusSeeder::class,
            RouteSeeder::class,
            ScheduleSeeder::class,
            ReservationSeeder::class,
        ]);
    }
}
