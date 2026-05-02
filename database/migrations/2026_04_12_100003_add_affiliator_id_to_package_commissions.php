<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tambah affiliator_id ke affiliate_package_commissions
     * untuk support komisi custom per affiliator per package
     */
    public function up()
    {
        Schema::table('affiliate_package_commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('affiliate_package_commissions', 'affiliator_id')) {
                $table->foreignId('affiliator_id')->nullable()->after('id')
                      ->constrained('affiliators')->onDelete('cascade');
            }
            
            // Index dengan nama yang lebih pendek
            if (!Schema::hasColumn('affiliate_package_commissions', 'affiliator_id')) {
                $table->index(['affiliator_id', 'package_id', 'is_active'], 'aff_pkg_comm_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('affiliate_package_commissions', function (Blueprint $table) {
            if (Schema::hasColumn('affiliate_package_commissions', 'affiliator_id')) {
                $table->dropForeign(['affiliator_id']);
                $table->dropIndex('aff_pkg_comm_idx');
                $table->dropColumn('affiliator_id');
            }
        });
    }
};
