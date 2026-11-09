<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('schedule_id');
            $table->foreignId('bus_id')->constrained('buses', 'bus_id')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes', 'route_id')->onDelete('cascade');
            $table->date('departure_date');
            $table->time('departure_time');
            $table->integer('available_seats');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};