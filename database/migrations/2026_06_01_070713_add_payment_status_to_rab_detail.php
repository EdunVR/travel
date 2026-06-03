<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rab_detail', function (Blueprint $table) {
            if (!Schema::hasColumn('rab_detail', 'payment_status')) {
                $table->string('payment_status', 20)->default('hutang')->after('realisasi_pemakaian');
            }
            if (!Schema::hasColumn('rab_detail', 'hutang_amount')) {
                $table->decimal('hutang_amount', 15, 2)->default(0)->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rab_detail', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'hutang_amount']);
        });
    }
};
