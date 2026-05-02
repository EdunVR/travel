<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_package_id')->constrained('travel_packages')->onDelete('cascade');
            $table->integer('day_number');
            $table->string('day_title');
            $table->date('day_date');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['travel_package_id', 'day_number']);
        });

        Schema::create('tour_plan_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_plan_id')->constrained('tour_plans')->onDelete('cascade');
            $table->time('activity_time');
            $table->string('activity_title');
            $table->text('activity_description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['tour_plan_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_plan_activities');
        Schema::dropIfExists('tour_plans');
    }
};
