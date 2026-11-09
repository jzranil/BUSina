<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = User::factory()->employee()->create();
    }

    private function validBus(array $overrides = []): array
    {
        return $overrides + [
            'bus_number' => 'bus010',
            'plate_number' => 'abc 1234',
            'bus_type' => 'aircon',
            'seating_capacity' => 45,
            'driver_name' => 'Pedro Reyes',
            'status' => 'active',
        ];
    }

    public function test_staff_can_add_a_bus(): void
    {
        $this->actingAs($this->employee)->post('/portal/buses', $this->validBus())
            ->assertRedirect(route('portal.buses.index'))
            ->assertSessionHas('success');

        // input is normalised to upper case
        $this->assertDatabaseHas('buses', ['bus_number' => 'BUS010', 'plate_number' => 'ABC 1234']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'bus.created']);
    }

    public function test_bus_validation_errors(): void
    {
        Bus::factory()->create(['bus_number' => 'BUS010']);

        $this->actingAs($this->employee)
            ->post('/portal/buses', $this->validBus(['plate_number' => '12-ABCD', 'seating_capacity' => 5, 'bus_type' => 'rocket']))
            ->assertSessionHasErrors(['bus_number', 'plate_number', 'seating_capacity', 'bus_type']);
    }

    public function test_a_bus_with_trips_cannot_be_deleted(): void
    {
        $schedule = Schedule::factory()->create();

        $this->actingAs($this->employee)->delete("/portal/buses/{$schedule->bus_id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('buses', ['bus_id' => $schedule->bus_id]);
    }

    public function test_route_destination_must_differ_and_be_unique(): void
    {
        BusRoute::factory()->create(['departure_location' => 'Manila', 'destination' => 'Baguio']);

        $base = ['route_name' => 'X', 'distance' => 100, 'duration_minutes' => 120, 'fare' => 300, 'is_active' => 1];

        $this->actingAs($this->employee)
            ->post('/portal/routes', $base + ['departure_location' => 'Manila', 'destination' => 'Manila'])
            ->assertSessionHasErrors('destination');

        $this->actingAs($this->employee)
            ->post('/portal/routes', $base + ['departure_location' => 'Manila', 'destination' => 'Baguio'])
            ->assertSessionHasErrors('destination');
    }

    public function test_staff_can_schedule_a_trip(): void
    {
        $bus = Bus::factory()->create();
        $route = BusRoute::factory()->create();

        $this->actingAs($this->employee)->post('/portal/schedules', [
            'bus_id' => $bus->bus_id,
            'route_id' => $route->route_id,
            'departure_date' => today()->addDays(2)->toDateString(),
            'departure_time' => '09:30',
        ])->assertRedirect(route('portal.schedules.index'));

        $this->assertDatabaseHas('schedules', ['bus_id' => $bus->bus_id, 'departure_time' => '09:30:00', 'status' => 'scheduled']);
    }

    public function test_trip_in_the_past_is_rejected(): void
    {
        $this->actingAs($this->employee)->post('/portal/schedules', [
            'bus_id' => Bus::factory()->create()->bus_id,
            'route_id' => BusRoute::factory()->create()->route_id,
            'departure_date' => today()->subDay()->toDateString(),
            'departure_time' => '09:30',
        ])->assertSessionHasErrors('departure_date');
    }

    public function test_a_bus_cannot_be_on_two_overlapping_trips(): void
    {
        // existing trip: 08:00, 5 hours long → busy until 13:00
        $existing = Schedule::factory()->create();

        $this->actingAs($this->employee)->post('/portal/schedules', [
            'bus_id' => $existing->bus_id,
            'route_id' => BusRoute::factory()->create()->route_id,
            'departure_date' => $existing->departure_date->toDateString(),
            'departure_time' => '10:00',
        ])->assertSessionHasErrors('bus_id');

        // after it arrives is fine
        $this->actingAs($this->employee)->post('/portal/schedules', [
            'bus_id' => $existing->bus_id,
            'route_id' => BusRoute::factory()->create()->route_id,
            'departure_date' => $existing->departure_date->toDateString(),
            'departure_time' => '14:00',
        ])->assertSessionHasNoErrors();
    }

    public function test_buses_under_maintenance_cannot_be_scheduled(): void
    {
        $this->actingAs($this->employee)->post('/portal/schedules', [
            'bus_id' => Bus::factory()->create(['status' => 'maintenance'])->bus_id,
            'route_id' => BusRoute::factory()->create()->route_id,
            'departure_date' => today()->addDays(2)->toDateString(),
            'departure_time' => '09:30',
        ])->assertSessionHasErrors('bus_id');
    }
}
