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
        Schema::table('company_settings', function (Blueprint $table) {
            // Agency fee settings (fee keagenan untuk rekrutmen mitra)
            $table->decimal('agency_fee_percentage', 5, 2)->default(10.00)->comment('Persentase fee keagenan dari komisi mitra yang direkrut');
            $table->decimal('agency_fee_fixed', 15, 2)->default(0)->comment('Fee keagenan tetap per transaksi mitra yang direkrut');
            $table->enum('agency_fee_type', ['percentage', 'fixed', 'both'])->default('percentage')->comment('Tipe perhitungan fee keagenan');
            $table->boolean('agency_fee_enabled')->default(true)->comment('Aktifkan/nonaktifkan fee keagenan');
            $table->integer('agency_fee_max_level')->default(1)->comment('Maksimal level downline yang dapat komisi (1=direct only, 2=2 level, dst)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'agency_fee_percentage',
                'agency_fee_fixed',
                'agency_fee_type',
                'agency_fee_enabled',
                'agency_fee_max_level'
            ]);
        });
    }
};
