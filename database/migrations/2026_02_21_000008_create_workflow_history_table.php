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
        Schema::create('workflow_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_travel_package');
            $table->string('from_stage')->nullable();
            $table->string('to_stage');
            $table->timestamp('transitioned_at');
            $table->unsignedBigInteger('transitioned_by');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_travel_package')->references('id')->on('travel_packages')->onDelete('cascade');
            
            // Indexes
            $table->index('id_travel_package');
            $table->index('transitioned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_history');
    }
};
