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
                if (!Schema::hasColumn('piutang', 'tanggal_piutang')) {
                    $table->date('tanggal_piutang')->nullable()->after('id_outlet');
                }
                if (!Schema::hasColumn('piutang', 'keterangan')) {
                    $table->text('keterangan')->nullable()->after('status');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('piutang', function (Blueprint $table) {
            if (Schema::hasColumn('piutang', 'tanggal_piutang')) {
                $table->dropColumn('tanggal_piutang');
            }
            if (Schema::hasColumn('piutang', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};
