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
        Schema::create('workflow_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_travel_package');
            $table->unsignedBigInteger('id_workflow_stage');
            $table->string('task_name');
            $table->text('task_description')->nullable();
            $table->string('assigned_to_team')->nullable();
            $table->unsignedBigInteger('assigned_to_user')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_travel_package')->references('id')->on('travel_packages')->onDelete('cascade');
            $table->foreign('id_workflow_stage')->references('id')->on('workflow_stages')->onDelete('cascade');
            
            // Indexes
            $table->index('id_travel_package');
            $table->index('id_workflow_stage');
            $table->index('assigned_to_team');
            $table->index('assigned_to_user');
            $table->index('status');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_tasks');
    }
};
