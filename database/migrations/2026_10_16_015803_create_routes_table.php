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
            $table->decimal('distance', 8, 2); // Distance in kilometers
            $table->string('duration'); // e.g., '3 hours 30 mins'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};