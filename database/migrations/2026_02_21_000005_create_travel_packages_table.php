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
        Schema::create('travel_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_code')->unique();
            $table->string('package_name');
            $table->enum('package_type', ['hajj', 'umrah']);
            $table->text('description')->nullable();
            $table->integer('duration_days')->unsigned();
            $table->date('departure_date');
            $table->date('return_date');
            $table->integer('capacity')->unsigned();
            $table->decimal('price', 15, 2);
            $table->decimal('hpp', 15, 2)->nullable();
            $table->decimal('profit_margin', 5, 2)->nullable();
            $table->enum('status', ['draft', 'active', 'full', 'completed', 'cancelled'])->default('draft');
            $table->string('current_workflow_stage')->default('product_analysis');
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('package_code');
            $table->index('package_type');
            $table->index('status');
            $table->index('current_workflow_stage');
            $table->index('id_outlet');
            $table->index(['departure_date', 'return_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_packages');
    }
};
