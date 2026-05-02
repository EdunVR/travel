<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_hierarchy_settings', function (Blueprint $table) {
            $table->enum('fee_type', ['percentage', 'flat'])->default('percentage')->after('percentage');
            $table->decimal('fee_value', 15, 2)->default(0)->after('fee_type')
                ->comment('Nilai fee: jika fee_type=percentage maka ini sama dengan percentage, jika flat maka ini nominal Rp');
        });

        // Sync fee_value dengan percentage yang sudah ada
        \DB::statement('UPDATE affiliate_hierarchy_settings SET fee_value = percentage WHERE fee_type = "percentage"');
    }

    public function down(): void
    {
        Schema::table('affiliate_hierarchy_settings', function (Blueprint $table) {
            $table->dropColumn(['fee_type', 'fee_value']);
        });
    }
};
