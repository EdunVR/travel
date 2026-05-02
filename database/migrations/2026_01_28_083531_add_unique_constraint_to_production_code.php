<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('productions')) {
            Schema::table('productions', function (Blueprint $table) {
                // Add unique constraint to production_code
                $table->unique('production_code', 'productions_production_code_unique');
            });
        }
    }

    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropUnique('productions_production_code_unique');
        });
    }
};