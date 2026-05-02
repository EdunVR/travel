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
        Schema::table('piutang', function (Blueprint $table) {
            // Add id_jamaah_booking column if it doesn't exist
            if (!Schema::hasColumn('piutang', 'id_jamaah_booking')) {
                $table->unsignedBigInteger('id_jamaah_booking')->nullable()->after('id_penjualan');
            }
            
            // Add tanggal_piutang column if it doesn't exist
            if (!Schema::hasColumn('piutang', 'tanggal_piutang')) {
                $table->date('tanggal_piutang')->nullable()->after('tanggal_tempo');
            }
            
            // Add keterangan column if it doesn't exist
            if (!Schema::hasColumn('piutang', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('piutang', function (Blueprint $table) {
            if (Schema::hasColumn('piutang', 'id_jamaah_booking')) {
                $table->dropColumn('id_jamaah_booking');
            }
            if (Schema::hasColumn('piutang', 'tanggal_piutang')) {
                $table->dropColumn('tanggal_piutang');
            }
            if (Schema::hasColumn('piutang', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};
