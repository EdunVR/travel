<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('info_paket_data')) {
            return;
        }

        Schema::create('info_paket_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_travel_package');
            $table->unsignedBigInteger('id_keberangkatan')->nullable();
            
            // Header info
            $table->string('group_name')->nullable();
            $table->string('tour_leader_name')->nullable();
            $table->integer('adult_count')->default(0);
            $table->integer('child_count')->default(0);
            $table->integer('infant_count')->default(0);
            
            // Itinerary rows (JSON array of {no, from, to, date, time, remark})
            $table->json('itinerary_rows')->nullable();
            
            // Rawdah schedule (JSON array of {no, activity, date, time})
            $table->json('rawdah_rows')->nullable();
            
            $table->timestamps();
            
            $table->index('id_travel_package');
            $table->index('id_keberangkatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_paket_data');
    }
};
