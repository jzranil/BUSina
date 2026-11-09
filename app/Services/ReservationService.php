<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * All reservation state changes go through here, so seat holding,
 * fares and activity logs stay consistent everywhere.
 *
 * Status flow:  pending → approved | rejected,   pending/approved → canceled
 * Rejecting or canceling releases the seats so others can book them.
 */
class ReservationService
{
    public const MAX_SEATS_PER_BOOKING = 10;

    /**
     * @param  array<int, array{seat_number:int, passenger_name:string}>  $passengers
     */
    public function create(User $customer, Schedule $schedule, array $passengers, string $contactName, string $contactNumber): Reservation
    {
        $schedule->loadMissing('bus', 'route');

        if (! $schedule->isBookable()) {
            throw ValidationException::withMessages([
                'schedule' => 'This trip is no longer open for booking.',
            ]);
        }

        $seatNumbers = array_map('intval', array_column($passengers, 'seat_number'));

        try {
            return DB::transaction(function () use ($customer, $schedule, $passengers, $seatNumbers, $contactName, $contactNumber) {
                $taken = $schedule->heldSeats()->whereIn('seat_number', $seatNumbers)->pluck('seat_number');

                if ($taken->isNotEmpty()) {
                    throw ValidationException::withMessages([
                        'passengers' => 'Seat '.$taken->sort()->implode(', ').' was just taken by someone else. Please choose another seat.',
                    ]);
                }

                $reservation = Reservation::create([
                    'booking_code' => $this->newBookingCode(),
                    'schedule_id' => $schedule->schedule_id,
                    'user_id' => $customer->user_id,
                    'contact_name' => $contactName,
                    'contact_number' => $contactNumber,
                    'passenger_count' => count($passengers),
                    'total_fare' => $schedule->route->fare * count($passengers),
                    'status' => 'pending',
                ]);

                foreach ($passengers as $passenger) {
                    $reservation->seats()->create([
                        'schedule_id' => $schedule->schedule_id,
                        'seat_number' => (int) $passenger['seat_number'],
                        'passenger_name' => $passenger['passenger_name'],
                        'is_held' => true,
                    ]);
                }

                ActivityLogger::log(
                    'reservation.created',
                    "{$customer->name} reserved seat ".implode(', ', $seatNumbers)." ({$reservation->booking_code}) on {$schedule->label()}",
                    $reservation,
                    $customer,
                );

                return $reservation;
            });
        } catch (UniqueConstraintViolationException) {
            // Two customers submitted the same seat at the same moment; the database index caught it.
            throw ValidationException::withMessages([
                'passengers' => 'One of your seats was just taken by someone else. Please choose again.',
            ]);
        }
    }

    public function approve(Reservation $reservation, User $staff): Reservation
    {
        $this->ensureStatus($reservation, ['pending'], 'Only pending reservations can be approved.');

        $reservation->update([
            'status' => 'approved',
            'reviewed_by' => $staff->user_id,
            'reviewed_at' => now(),
        ]);

        ActivityLogger::log('reservation.approved', "{$staff->name} approved {$reservation->booking_code}", $reservation, $staff);

        return $reservation;
    }

    public function reject(Reservation $reservation, User $staff, ?string $remarks = null): Reservation
    {
        $this->ensureStatus($reservation, ['pending'], 'Only pending reservations can be rejected.');

        DB::transaction(function () use ($reservation, $staff, $remarks) {
            $reservation->update([
                'status' => 'rejected',
                'reviewed_by' => $staff->user_id,
                'reviewed_at' => now(),
                'remarks' => $remarks,
            ]);
            $this->releaseSeats($reservation);
        });

        ActivityLogger::log('reservation.rejected', "{$staff->name} rejected {$reservation->booking_code}".($remarks ? ": {$remarks}" : ''), $reservation, $staff);

        return $reservation;
    }

    public function cancel(Reservation $reservation, User $actor): Reservation
    {
        $this->ensureStatus($reservation, ['pending', 'approved'], 'This reservation can no longer be canceled.');

        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'canceled']);
            $this->releaseSeats($reservation);
        });

        ActivityLogger::log('reservation.canceled', "{$actor->name} canceled {$reservation->booking_code}", $reservation, $actor);

        return $reservation;
    }

    private function releaseSeats(Reservation $reservation): void
    {
        $reservation->seats()->update(['is_held' => null]);
    }

    private function ensureStatus(Reservation $reservation, array $allowed, string $message): void
    {
        if (! in_array($reservation->status, $allowed, true)) {
            throw ValidationException::withMessages(['status' => $message]);
        }
    }

    /** e.g. BSN-7K2Q9X */
    private function newBookingCode(): string
    {
        do {
            $code = 'BSN-'.Str::upper(Str::random(6));
        } while (Reservation::where('booking_code', $code)->exists());

        return $code;
    }
}
