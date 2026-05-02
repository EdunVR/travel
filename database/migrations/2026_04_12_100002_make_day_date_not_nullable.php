<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, update any existing NULL values with a default date
        DB::table('tour_plans')
            ->whereNull('day_date')
            ->update(['day_date' => DB::raw('DATE_ADD(NOW(), INTERVAL (day_number - 1) DAY)')]);

        // Then make the column NOT NULL
        Schema::table('tour_plans', function (Blueprint $table) {
            $table->date('day_date')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tour_plans', function (Blueprint $table) {
            $table->date('day_date')->nullable()->change();
        });
    }
};
