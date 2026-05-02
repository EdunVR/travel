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
        Schema::create('hpp_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_travel_package');
            $table->decimal('flight_cost', 15, 2)->default(0);
            $table->decimal('hotel_cost', 15, 2)->default(0);
            $table->decimal('transportation_cost', 15, 2)->default(0);
            $table->decimal('meal_cost', 15, 2)->default(0);
            $table->decimal('visa_cost', 15, 2)->default(0);
            $table->decimal('guide_cost', 15, 2)->default(0);
            $table->decimal('insurance_cost', 15, 2)->default(0);
            $table->decimal('operational_overhead', 15, 2)->default(0);
            $table->decimal('contingency', 15, 2)->default(0);
            $table->decimal('total_hpp', 15, 2)->default(0);
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_travel_package')->references('id')->on('travel_packages')->onDelete('cascade');
            
            // Indexes
            $table->index('id_travel_package');
            $table->index('is_locked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpp_calculations');
    }
};
