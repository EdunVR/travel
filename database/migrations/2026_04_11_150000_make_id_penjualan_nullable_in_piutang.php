<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('piutang')) {
            Schema::table('piutang', function (Blueprint $table) {
                $table->unsignedBigInteger('id_penjualan')->nullable()->change();
            });
        }
    }

    public function down()
    {
        Schema::table('piutang', function (Blueprint $table) {
            $table->unsignedBigInteger('id_penjualan')->nullable(false)->change();
        });
    }
};
