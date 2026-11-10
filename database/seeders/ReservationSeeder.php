<?php

namespace Database\Seeders;

use App\Models\BusRoute;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Database\Seeder;

/**
 * Sample bookings made through the real ReservationService,
 * so seats, fares and activity logs look exactly like real usage.
 */
class ReservationSeeder extends Seeder
{
    public function run(ReservationService $service): void
    {
        $juan = User::where('email', 'customer@busina.test')->firstOrFail();
        $maria = User::where('email', 'maria@busina.test')->firstOrFail();
        $employee = User::where('email', 'employee@busina.test')->firstOrFail();

        $trip = fn (string $route, int $day) => Schedule::where('route_id', BusRoute::where('route_name', $route)->value('route_id'))
            ->whereDate('departure_date', today()->addDays($day))
            ->firstOrFail();

        // Juan: approved trip to Baguio for two people
        $approved = $service->create($juan, $trip('Manila – Baguio', 2), [
            ['seat_number' => 5, 'passenger_name' => 'Juan Dela Cruz'],
            ['seat_number' => 6, 'passenger_name' => 'Ana Dela Cruz'],
        ], 'Juan Dela Cruz', '09171234567');
        $service->approve($approved, $employee);

        // Juan: pending trip to Batangas
        $service->create($juan, $trip('Manila – Batangas', 4), [
            ['seat_number' => 12, 'passenger_name' => 'Juan Dela Cruz'],
        ], 'Juan Dela Cruz', '09171234567');

        // Maria: pending family trip to Baler
        $service->create($maria, $trip('Manila – Baler', 3), [
            ['seat_number' => 1, 'passenger_name' => 'Maria Santos'],
            ['seat_number' => 2, 'passenger_name' => 'Jose Santos'],
            ['seat_number' => 3, 'passenger_name' => 'Lea Santos'],
        ], 'Maria Santos', '+639281234567');

        // Maria: rejected booking (seat released again)
        $rejected = $service->create($maria, $trip('Manila – Baguio', 2), [
            ['seat_number' => 7, 'passenger_name' => 'Maria Santos'],
        ], 'Maria Santos', '+639281234567');
        $service->reject($rejected, $employee, 'Duplicate booking');
    }
}
