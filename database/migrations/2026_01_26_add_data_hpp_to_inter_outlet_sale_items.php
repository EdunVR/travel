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
        if (Schema::hasTable('inter_outlet_sale_items') && !Schema::hasColumn('inter_outlet_sale_items', 'data_hpp')) {
            Schema::table('inter_outlet_sale_items', function (Blueprint $table) {
                $table->json('data_hpp')->nullable()->after('subtotal')->comment('Data HPP yang digunakan saat transaksi dalam format JSON: [{"id_hpp": 123, "hpp": 2500, "qty_used": 1000}, ...]');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inter_outlet_sale_items', function (Blueprint $table) {
            $table->dropColumn('data_hpp');
        });
    }
};