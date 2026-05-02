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
        if (Schema::hasTable('service_invoices') && !Schema::hasColumn('service_invoices', 'catatan')) {
            Schema::table('service_invoices', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('keterangan_service');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_invoices', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
