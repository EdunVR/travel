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
        Schema::create('design_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_travel_package');
            $table->enum('material_type', ['flyer', 'itinerary', 'promotional_video', 'package_information']);
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('version')->unsigned()->default(1);
            $table->boolean('is_complete')->default(false);
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_travel_package')->references('id')->on('travel_packages')->onDelete('cascade');
            
            // Indexes
            $table->index('id_travel_package');
            $table->index('material_type');
            $table->index('is_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_materials');
    }
};
