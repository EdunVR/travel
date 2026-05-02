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
        Schema::create('hotel_room_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_hotel');
            $table->string('room_type_name');
            $table->integer('capacity')->unsigned();
            $table->integer('total_rooms')->unsigned();
            $table->decimal('price_per_night', 15, 2);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_hotel')->references('id')->on('hotels')->onDelete('cascade');
            
            // Indexes
            $table->index('id_hotel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_room_types');
    }
};
