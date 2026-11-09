<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id('bus_id');
            $table->string('bus_number')->unique();
            $table->integer('seating_capacity');
            $table->string('driver_name');
            $table->string('departure_location');
            $table->string('destination');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};