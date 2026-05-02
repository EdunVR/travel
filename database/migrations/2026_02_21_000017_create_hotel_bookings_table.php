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
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_hotel');
            $table->unsignedBigInteger('id_keberangkatan');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('room_count')->unsigned();
            $table->string('booking_reference')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_hotel')->references('id')->on('hotels')->onDelete('cascade');
            $table->foreign('id_keberangkatan')->references('id')->on('keberangkatan')->onDelete('cascade');
            
            // Indexes
            $table->index('id_hotel');
            $table->index('id_keberangkatan');
            $table->index('status');
            $table->index(['check_in_date', 'check_out_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};
