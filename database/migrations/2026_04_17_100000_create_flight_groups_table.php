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
        Schema::create('flight_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('id_outlet');
            $table->timestamps();
            
            $table->foreign('id_outlet')->references('id_outlet')->on('outlets')->onDelete('cascade');
        });
        
        Schema::create('flight_group_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_flight_group');
            $table->unsignedBigInteger('id_flight');
            $table->enum('flight_type', ['departure', 'return', 'transit'])->default('departure');
            $table->integer('sequence')->default(0);
            $table->timestamps();
            
            $table->foreign('id_flight_group')->references('id')->on('flight_groups')->onDelete('cascade');
            $table->foreign('id_flight')->references('id')->on('flights')->onDelete('cascade');
            
            $table->unique(['id_flight_group', 'id_flight']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_group_items');
        Schema::dropIfExists('flight_groups');
    }
};
