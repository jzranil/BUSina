<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations', 'reservation_id')->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained('schedules', 'schedule_id')->restrictOnDelete();
            $table->unsignedSmallInteger('seat_number');
            $table->string('passenger_name');
            // TRUE while the seat is taken, NULL once released (reservation rejected or canceled).
            // A unique index allows many NULLs, so a released seat can be booked again,
            // but the database itself refuses two active bookings of the same seat on the same trip.
            $table->boolean('is_held')->nullable()->default(true);
            $table->timestamps();

            $table->unique(['schedule_id', 'seat_number', 'is_held'], 'seat_not_double_booked');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_seats');
    }
};
