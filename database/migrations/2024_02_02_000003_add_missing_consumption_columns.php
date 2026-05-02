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
        // Add fully_consumed to production_materials if table and column don't exist
        if (Schema::hasTable('production_materials') && !Schema::hasColumn('production_materials', 'fully_consumed')) {
            Schema::table('production_materials', function (Blueprint $table) {
                $table->boolean('fully_consumed')->default(false)->after('quantity_used');
            });
        }

        // Add materials_consumed_fully to productions if table and column don't exist
        if (Schema::hasTable('productions') && !Schema::hasColumn('productions', 'materials_consumed_fully')) {
            Schema::table('productions', function (Blueprint $table) {
                $table->boolean('materials_consumed_fully')->default(false)->after('created_by');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_materials', function (Blueprint $table) {
            if (Schema::hasColumn('production_materials', 'fully_consumed')) {
                $table->dropColumn('fully_consumed');
            }
        });

        Schema::table('productions', function (Blueprint $table) {
            if (Schema::hasColumn('productions', 'materials_consumed_fully')) {
                $table->dropColumn('materials_consumed_fully');
            }
        });
    }
};