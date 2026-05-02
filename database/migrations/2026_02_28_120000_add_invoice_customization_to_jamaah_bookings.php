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
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            // Only add terms_conditions if it doesn't exist
            if (!Schema::hasColumn('jamaah_bookings', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('upgrade_notes');
            }
            
            // Only add seller_name if it doesn't exist
            if (!Schema::hasColumn('jamaah_bookings', 'seller_name')) {
                $table->string('seller_name')->nullable()->after('terms_conditions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('jamaah_bookings', 'terms_conditions')) {
                $table->dropColumn('terms_conditions');
            }
            if (Schema::hasColumn('jamaah_bookings', 'seller_name')) {
                $table->dropColumn('seller_name');
            }
        });
    }
};
