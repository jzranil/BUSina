<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A bus is only the vehicle. Where and when it travels lives in routes and schedules.
        Schema::create('buses', function (Blueprint $table) {
            $table->id('bus_id');
            $table->string('bus_number')->unique();
            $table->string('plate_number', 10)->unique();
            $table->enum('bus_type', ['ordinary', 'aircon', 'deluxe'])->default('aircon');
            $table->unsignedSmallInteger('seating_capacity');
            $table->string('driver_name');
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
