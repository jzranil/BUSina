<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One reservation = one booking by a customer, covering one or more seats (see reservation_seats).
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');
            $table->string('booking_code', 12)->unique();
            $table->foreignId('schedule_id')->constrained('schedules', 'schedule_id')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->string('contact_name');
            $table->string('contact_number', 20);
            $table->unsignedTinyInteger('passenger_count');
            $table->decimal('total_fare', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
