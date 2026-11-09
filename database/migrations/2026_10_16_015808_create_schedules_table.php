<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Available seats are not stored: they are computed from the bus capacity minus held seats,
        // so the number can never drift out of sync with the real bookings.
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('schedule_id');
            // restrictOnDelete: a bus or route that still has trips cannot be deleted by accident
            $table->foreignId('bus_id')->constrained('buses', 'bus_id')->restrictOnDelete();
            $table->foreignId('route_id')->constrained('routes', 'route_id')->restrictOnDelete();
            $table->date('departure_date');
            $table->time('departure_time');
            $table->enum('status', ['scheduled', 'departed', 'completed', 'canceled'])->default('scheduled');
            $table->timestamps();

            $table->index(['departure_date', 'route_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
