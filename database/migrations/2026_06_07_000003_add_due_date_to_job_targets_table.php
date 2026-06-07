<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds nullable due_date column to job_targets table after the period column.
     */
    public function up(): void
    {
        Schema::table('job_targets', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_targets', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
