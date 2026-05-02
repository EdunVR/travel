<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hpp_calculations', function (Blueprint $table) {
            // Nilai penyesuaian laporan keuangan (surplus: positif, defisit: negatif)
            $table->decimal('laporan_adjustment', 15, 2)->default(0)->after('component_realisasi')
                ->comment('Penyesuaian laporan: surplus (+) dikurangi dari costs, defisit (-) ditambah ke costs');
            $table->boolean('laporan_disesuaikan')->default(false)->after('laporan_adjustment');
            $table->timestamp('laporan_disesuaikan_at')->nullable()->after('laporan_disesuaikan');
        });
    }

    public function down(): void
    {
        Schema::table('hpp_calculations', function (Blueprint $table) {
            $table->dropColumn(['laporan_adjustment', 'laporan_disesuaikan', 'laporan_disesuaikan_at']);
        });
    }
};
