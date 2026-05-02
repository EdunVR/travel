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
        Schema::table('flights', function (Blueprint $table) {
            // Tambahkan kolom price_per_person jika belum ada
            if (!Schema::hasColumn('flights', 'price_per_person')) {
                $table->decimal('price_per_person', 15, 2)->nullable()->after('capacity');
            }
            
            // Tambahkan kolom seller_name jika belum ada
            if (!Schema::hasColumn('flights', 'seller_name')) {
                $table->string('seller_name')->nullable()->after('aircraft_type');
            }
            
            // Tambahkan kolom seller_phone jika belum ada
            if (!Schema::hasColumn('flights', 'seller_phone')) {
                $table->string('seller_phone')->nullable()->after('seller_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            if (Schema::hasColumn('flights', 'price_per_person')) {
                $table->dropColumn('price_per_person');
            }
            if (Schema::hasColumn('flights', 'seller_name')) {
                $table->dropColumn('seller_name');
            }
            if (Schema::hasColumn('flights', 'seller_phone')) {
                $table->dropColumn('seller_phone');
            }
        });
    }
};
