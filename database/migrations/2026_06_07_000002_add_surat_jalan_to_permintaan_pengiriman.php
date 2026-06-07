<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_pengiriman', function (Blueprint $table) {
            if (!Schema::hasColumn('permintaan_pengiriman', 'nomor_surat_jalan')) {
                $table->string('nomor_surat_jalan')->nullable()->after('no_permintaan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_pengiriman', function (Blueprint $table) {
            $table->dropColumn('nomor_surat_jalan');
        });
    }
};
