<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jamaah_booking');
            $table->string('nama');
            $table->text('keterangan')->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->integer('qty')->default(1);
            $table->boolean('masuk_hpp')->default(true);
            $table->timestamps();

            $table->foreign('id_jamaah_booking')->references('id')->on('jamaah_bookings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_addons');
    }
};
