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
        Schema::create('hotel_room_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_hotel_booking');
            $table->unsignedBigInteger('id_jamaah_booking');
            $table->string('room_number')->nullable();
            $table->string('room_type')->nullable();
            $table->integer('bed_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_hotel_booking')->references('id')->on('hotel_bookings')->onDelete('cascade');
            $table->foreign('id_jamaah_booking')->references('id')->on('jamaah_bookings')->onDelete('cascade');
            
            // Indexes
            $table->index('id_hotel_booking');
            $table->index('id_jamaah_booking');
            $table->index('room_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_room_assignments');
    }
};
