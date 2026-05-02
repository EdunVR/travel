<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('affiliators', function (Blueprint $table) {
            // Hanya tambahkan kolom yang belum ada
            if (!Schema::hasColumn('affiliators', 'payment_proof')) {
                $table->string('payment_proof')->nullable();
            }
            
            if (!Schema::hasColumn('affiliators', 'payment_verified_at')) {
                $table->timestamp('payment_verified_at')->nullable();
            }
            
            if (!Schema::hasColumn('affiliators', 'ppc_commission')) {
                $table->decimal('ppc_commission', 10, 2)->default(50)
                      ->comment('Pay Per Click commission in IDR');
            }
            
            if (!Schema::hasColumn('affiliators', 'min_sale_commission')) {
                $table->decimal('min_sale_commission', 15, 2)->default(500000)
                      ->comment('Minimum sale commission based on program');
            }
        });
    }

    public function down()
    {
        Schema::table('affiliators', function (Blueprint $table) {
            $columnsToCheck = [
                'payment_proof',
                'payment_verified_at',
                'ppc_commission',
                'min_sale_commission'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('affiliators', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
