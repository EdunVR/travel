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
        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                if (!Schema::hasColumn('attendances', 'clock_in_photo')) {
                    $table->string('clock_in_photo')->nullable()->after('clock_in');
                }
                if (!Schema::hasColumn('attendances', 'clock_out_photo')) {
                    $table->string('clock_out_photo')->nullable()->after('clock_out');
                }
                if (!Schema::hasColumn('attendances', 'break_in_photo')) {
                    $table->string('break_in_photo')->nullable()->after('break_in');
                }
                if (!Schema::hasColumn('attendances', 'break_out_photo')) {
                    $table->string('break_out_photo')->nullable()->after('break_out');
                }
                if (!Schema::hasColumn('attendances', 'overtime_in_photo')) {
                    $table->string('overtime_in_photo')->nullable()->after('overtime_in');
                }
                if (!Schema::hasColumn('attendances', 'overtime_out_photo')) {
                    $table->string('overtime_out_photo')->nullable()->after('overtime_out');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'clock_in_photo',
                'clock_out_photo', 
                'break_in_photo',
                'break_out_photo',
                'overtime_in_photo',
                'overtime_out_photo'
            ]);
        });
    }
};