<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_flight');
            $table->unsignedBigInteger('id_keberangkatan');
            $table->integer('seat_count')->unsigned();
            $table->string('booking_reference')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'ticketed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_flight')->references('id')->on('flights')->onDelete('cascade');
            $table->foreign('id_keberangkatan')->references('id')->on('keberangkatan')->onDelete('cascade');
            
            // Indexes
            $table->index('id_flight');
            $table->index('id_keberangkatan');
            $table->index('status');
            $table->index('booking_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
    }
};
