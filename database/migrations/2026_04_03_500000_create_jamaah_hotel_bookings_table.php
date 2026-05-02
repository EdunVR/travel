<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jamaah_hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jamaah_booking');
            $table->unsignedBigInteger('id_hotel');
            $table->string('city_type')->default('makkah'); // makkah, madinah
            $table->string('room_type')->nullable(); // quad, triple, double, single
            $table->date('check_in_date')->nullable();
            $table->date('check_out_date')->nullable();
            $table->integer('nights')->default(0);
            $table->decimal('price_per_night', 15, 2)->default(0);
            $table->boolean('is_charged')->default(false); // true = charge ke invoice, false = include paket
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('id_jamaah_booking')->references('id')->on('jamaah_bookings')->onDelete('cascade');
            $table->foreign('id_hotel')->references('id')->on('hotels')->onDelete('cascade');
            $table->index('id_jamaah_booking');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaah_hotel_bookings');
    }
};
