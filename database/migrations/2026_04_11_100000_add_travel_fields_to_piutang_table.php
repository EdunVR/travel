<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('piutang')) {
            Schema::table('piutang', function (Blueprint $table) {
                if (!Schema::hasColumn('piutang', 'id_jamaah_booking')) {
                    $table->unsignedBigInteger('id_jamaah_booking')->nullable()->after('id_penjualan');
                }
                if (!Schema::hasColumn('piutang', 'source_type')) {
                    $table->string('source_type')->default('penjualan')->after('id_jamaah_booking'); // 'penjualan' or 'travel'
                }
                
                // Add index for faster queries
                $table->index('id_jamaah_booking');
                $table->index('source_type');
            });
        }
    }

    public function down(): void
    {
        Schema::table('piutang', function (Blueprint $table) {
            $table->dropIndex(['id_jamaah_booking']);
            $table->dropIndex(['source_type']);
            $table->dropColumn(['id_jamaah_booking', 'source_type']);
        });
    }
};
