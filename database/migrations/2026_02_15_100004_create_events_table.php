<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // 'booking' or 'travel'
            $table->string('name');
            $table->date('date');

            // Booking-specific fields (nullable)
            $table->time('set_time_from')->nullable();
            $table->time('set_time_to')->nullable();
            $table->text('set_info')->nullable();
            $table->text('extra_information')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('venue_location')->nullable();
            $table->string('hotel_name')->nullable();
            $table->string('hotel_location')->nullable();
            $table->text('hotel_extra_info')->nullable();
            $table->string('status')->nullable(); // option, confirmed, cancelled

            // Travel-specific fields (nullable)
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->string('flight_number')->nullable();
            $table->string('leave_from_name')->nullable();
            $table->string('leave_from_location')->nullable();
            $table->string('arrival_at_name')->nullable();
            $table->string('arrival_at_location')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
