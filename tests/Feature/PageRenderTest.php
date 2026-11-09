<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Opens every page as the right role, using the demo seed data,
 * so a broken view or missing variable fails the test suite.
 */
class PageRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_pages_render(): void
    {
        $trip = Schedule::bookable()->firstOrFail();

        foreach (['/', '/trips', '/trips?from=Manila&to=Baguio&passengers=2', '/routes', "/trips/{$trip->schedule_id}?passengers=2", '/login', '/register', '/forgot-password'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/trips')->assertSee('Select seats');
        $this->get("/trips/{$trip->schedule_id}")->assertSee('Log in to book');
    }

    public function test_staff_portal_pages_render_for_admin(): void
    {
        $admin = User::where('email', 'admin@busina.test')->firstOrFail();
        $bus = Bus::firstOrFail();
        $route = BusRoute::firstOrFail();
        $schedule = Schedule::has('reservations')->firstOrFail();
        $reservation = Reservation::firstOrFail();
        $user = User::where('role', 'customer')->firstOrFail();

        $urls = [
            '/portal', '/portal/reservations', '/portal/reservations?status=pending', "/portal/reservations/{$reservation->reservation_id}",
            '/portal/buses', '/portal/buses?q=BUS&status=active', '/portal/buses/create', "/portal/buses/{$bus->bus_id}", "/portal/buses/{$bus->bus_id}/edit",
            '/portal/routes', '/portal/routes/create', "/portal/routes/{$route->route_id}", "/portal/routes/{$route->route_id}/edit",
            '/portal/schedules', '/portal/schedules?status=scheduled', '/portal/schedules/create', "/portal/schedules/{$schedule->schedule_id}", "/portal/schedules/{$schedule->schedule_id}/edit",
            '/portal/users', '/portal/users/create', "/portal/users/{$user->user_id}/edit",
            '/portal/activity-logs', '/portal/activity-logs?type=reservation',
            '/user/profile',
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->actingAs($admin)->get('/portal')->assertSee('Pending approval')->assertSee('Activity Logs');
    }

    public function test_employee_sees_portal_without_admin_links(): void
    {
        $employee = User::where('email', 'employee@busina.test')->firstOrFail();

        $this->actingAs($employee)->get('/portal')->assertOk()->assertDontSee('Activity Logs');
        $this->actingAs($employee)->get('/user/profile')->assertOk();
    }

    public function test_customer_pages_render(): void
    {
        $customer = User::where('email', 'customer@busina.test')->firstOrFail();
        $reservation = $customer->reservations()->firstOrFail();
        $trip = Schedule::bookable()->firstOrFail();

        foreach (['/my/dashboard', '/my/reservations', '/my/reservations?status=approved', "/my/reservations/{$reservation->reservation_id}", "/trips/{$trip->schedule_id}", '/user/profile'] as $url) {
            $this->actingAs($customer)->get($url)->assertOk();
        }

        $this->actingAs($customer)->get("/trips/{$trip->schedule_id}")->assertSee('Reserve seats');
    }

    public function test_friendly_error_pages(): void
    {
        $this->get('/trips/999999')->assertNotFound()->assertSee('Page not found')->assertDontSee('No query results');

        $customer = User::where('email', 'customer@busina.test')->firstOrFail();
        $this->actingAs($customer)->get('/portal')->assertForbidden()->assertSee('permission');
    }
}
