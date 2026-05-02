<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('room_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_keberangkatan');
            $table->string('city_type'); // makkah, madinah
            $table->string('room_number'); // e.g. "101", "A1"
            $table->string('room_type')->default('double'); // double, triple, quad
            $table->string('person_type'); // jamaah, family
            $table->unsignedBigInteger('id_jamaah_booking')->nullable(); // booking jamaah utama
            $table->string('person_name'); // nama orang
            $table->string('family_index')->nullable(); // index anggota keluarga jika family
            $table->string('room_position')->nullable(); // catatan berdekatan
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('id_keberangkatan')->references('id')->on('keberangkatan')->onDelete('cascade');
            $table->foreign('id_jamaah_booking')->references('id')->on('jamaah_bookings')->onDelete('cascade');
            $table->index(['id_keberangkatan', 'city_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_assignments');
    }
};
