<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add day_date column to tour_plans table
        Schema::table('tour_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_plans', 'day_date')) {
                $table->date('day_date')->after('day_title')->nullable();
            }
        });

        // Remove activity_date column from tour_plan_activities table if exists
        if (Schema::hasTable('tour_plan_activities') && Schema::hasColumn('tour_plan_activities', 'activity_date')) {
            Schema::table('tour_plan_activities', function (Blueprint $table) {
                $table->dropColumn('activity_date');
            });
        }
    }

    public function down(): void
    {
        // Remove day_date column from tour_plans table
        Schema::table('tour_plans', function (Blueprint $table) {
            $table->dropColumn('day_date');
        });

        // Add back activity_date column to tour_plan_activities table
        Schema::table('tour_plan_activities', function (Blueprint $table) {
            $table->date('activity_date')->after('activity_title');
        });
    }
};
