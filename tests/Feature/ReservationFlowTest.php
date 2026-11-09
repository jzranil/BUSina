<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    private Schedule $trip;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trip = Schedule::factory()->create(); // 45-seat bus, fare 750
        $this->customer = User::factory()->customer()->create();
    }

    private function book(User $customer, array $seats, array $overrides = [])
    {
        return $this->actingAs($customer)->post("/trips/{$this->trip->schedule_id}/reserve", $overrides + [
            'contact_name' => $customer->name,
            'contact_number' => '0917 123 4567',
            'passengers' => array_map(fn ($seat) => ['seat_number' => $seat, 'passenger_name' => "Passenger {$seat}"], $seats),
        ]);
    }

    public function test_customer_can_book_seats(): void
    {
        $this->book($this->customer, [3, 4])->assertRedirect()->assertSessionHas('success');

        $reservation = Reservation::firstOrFail();
        $this->assertSame('pending', $reservation->status);
        $this->assertSame(2, $reservation->passenger_count);
        $this->assertEquals(1500, $reservation->total_fare);
        $this->assertSame('09171234567', $reservation->contact_number);
        $this->assertEqualsCanonicalizing([3, 4], $this->trip->takenSeatNumbers());
        $this->assertDatabaseHas('activity_logs', ['action' => 'reservation.created']);
    }

    public function test_a_seat_cannot_be_booked_twice(): void
    {
        $this->book($this->customer, [10]);
        $this->book(User::factory()->customer()->create(), [10, 11])->assertSessionHasErrors('passengers');

        $this->assertSame(1, Reservation::count());
    }

    public function test_the_database_itself_refuses_a_double_booked_seat(): void
    {
        $this->book($this->customer, [20]);
        $reservation = Reservation::firstOrFail();

        // Simulates two requests racing past the application check at the same moment
        $this->expectException(UniqueConstraintViolationException::class);
        $reservation->seats()->create([
            'schedule_id' => $this->trip->schedule_id,
            'seat_number' => 20,
            'passenger_name' => 'Racer',
            'is_held' => true,
        ]);
    }

    public function test_booking_validation(): void
    {
        $this->book($this->customer, [46], ['contact_number' => '12345'])
            ->assertSessionHasErrors(['contact_number', 'passengers.0.seat_number']);

        $this->book($this->customer, [5, 5])->assertSessionHasErrors('passengers.1.seat_number');
        $this->book($this->customer, [])->assertSessionHasErrors('passengers');
    }

    public function test_staff_cannot_book_as_a_customer(): void
    {
        $this->book(User::factory()->employee()->create(), [1])->assertForbidden();
    }

    public function test_trips_that_already_left_cannot_be_booked(): void
    {
        $this->trip->update(['departure_date' => today()->subDay()->toDateString()]);

        $this->book($this->customer, [1])->assertSessionHasErrors('schedule');
    }

    public function test_staff_approve_and_reject(): void
    {
        $staff = User::factory()->employee()->create();
        $this->book($this->customer, [1]);
        $this->book(User::factory()->customer()->create(), [2]);
        [$first, $second] = Reservation::orderBy('reservation_id')->get();

        $this->actingAs($staff)->patch("/portal/reservations/{$first->reservation_id}/approve")->assertSessionHas('success');
        $this->assertSame('approved', $first->fresh()->status);
        $this->assertSame($staff->user_id, $first->fresh()->reviewed_by);

        $this->actingAs($staff)->patch("/portal/reservations/{$second->reservation_id}/reject", ['remarks' => 'Invalid details']);
        $this->assertSame('rejected', $second->fresh()->status);
        $this->assertSame([1], $this->trip->takenSeatNumbers(), 'Rejected seat 2 should be free again');

        // an approved reservation cannot be approved again
        $this->actingAs($staff)->patch("/portal/reservations/{$first->reservation_id}/approve")->assertSessionHasErrors('status');
    }

    public function test_a_released_seat_can_be_booked_again(): void
    {
        $this->book($this->customer, [8]);
        $reservation = Reservation::firstOrFail();

        $this->actingAs($this->customer)->patch("/my/reservations/{$reservation->reservation_id}/cancel")->assertSessionHas('success');
        $this->assertSame('canceled', $reservation->fresh()->status);

        $this->book(User::factory()->customer()->create(), [8])->assertSessionHasNoErrors();
        $this->assertSame([8], $this->trip->takenSeatNumbers());
    }

    public function test_customers_cannot_see_or_cancel_other_peoples_reservations(): void
    {
        $this->book($this->customer, [9]);
        $reservation = Reservation::firstOrFail();
        $stranger = User::factory()->customer()->create();

        $this->actingAs($stranger)->get("/my/reservations/{$reservation->reservation_id}")->assertForbidden();
        $this->actingAs($stranger)->patch("/my/reservations/{$reservation->reservation_id}/cancel")->assertForbidden();
        $this->assertSame('pending', $reservation->fresh()->status);
    }
}
