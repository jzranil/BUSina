<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id('route_id');
            $table->string('route_name');
            $table->string('departure_location');
            $table->string('destination');
            $table->decimal('distance', 8, 2);                 // kilometers
            $table->unsignedSmallInteger('duration_minutes');  // travel time, used to compute arrival
            $table->decimal('fare', 8, 2);                      // price per seat, in PHP
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['departure_location', 'destination']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
