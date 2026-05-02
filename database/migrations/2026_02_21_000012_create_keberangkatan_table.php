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
        Schema::create('keberangkatan', function (Blueprint $table) {
            $table->id();
            $table->string('keberangkatan_code')->unique();
            $table->string('keberangkatan_name');
            $table->unsignedBigInteger('id_travel_package');
            $table->date('departure_date');
            $table->date('return_date');
            $table->integer('total_jamaah')->unsigned()->default(0);
            $table->enum('status', ['planning', 'confirmed', 'departed', 'completed'])->default('planning');
            $table->unsignedBigInteger('id_rab')->nullable();
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_travel_package')->references('id')->on('travel_packages')->onDelete('cascade');
            
            // Indexes
            $table->index('keberangkatan_code');
            $table->index('id_travel_package');
            $table->index('status');
            $table->index('id_outlet');
            $table->index(['departure_date', 'return_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keberangkatan');
    }
};
