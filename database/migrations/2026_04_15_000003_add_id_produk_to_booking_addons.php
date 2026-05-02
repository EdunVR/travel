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
        Schema::table('booking_addons', function (Blueprint $table) {
            // Add id_produk column if it doesn't exist
            if (!Schema::hasColumn('booking_addons', 'id_produk')) {
                $table->unsignedBigInteger('id_produk')->nullable()->after('id_jamaah_booking');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_addons', function (Blueprint $table) {
            if (Schema::hasColumn('booking_addons', 'id_produk')) {
                $table->dropColumn('id_produk');
            }
        });
    }
};
