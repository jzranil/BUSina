<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

/**
 * Customers may only see and cancel their own reservations.
 * Staff (admin, employee) may see all, approve, reject and cancel.
 */
class ReservationPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->isStaff() || $this->owns($user, $reservation);
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        if (! $user->isStaff() && ! $this->owns($user, $reservation)) {
            return false;
        }

        return $reservation->isActive() && $reservation->schedule->departsAt()->isFuture();
    }

    public function review(User $user, Reservation $reservation): bool
    {
        return $user->isStaff();
    }

    private function owns(User $user, Reservation $reservation): bool
    {
        return $reservation->user_id !== null && (int) $reservation->user_id === (int) $user->user_id;
    }
}
