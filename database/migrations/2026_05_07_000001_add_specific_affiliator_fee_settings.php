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
        Schema::table('affiliate_hierarchy_settings', function (Blueprint $table) {
            // Tambah kolom untuk fee setting spesifik per mitra
            $table->unsignedBigInteger('from_affiliator_id')->nullable()->after('to_level');
            $table->unsignedBigInteger('to_affiliator_id')->nullable()->after('from_affiliator_id');
            
            // Add foreign keys
            $table->foreign('from_affiliator_id')->references('id')->on('affiliators')->onDelete('cascade');
            $table->foreign('to_affiliator_id')->references('id')->on('affiliators')->onDelete('cascade');
            
            // Drop unique constraint lama
            $table->dropUnique(['from_level', 'to_level']);
            
            // Add new composite unique untuk mencegah duplikat
            // Jika from_affiliator_id dan to_affiliator_id NULL = setting global
            // Jika ada value = setting spesifik untuk pasangan mitra tersebut
            $table->unique(['from_level', 'to_level', 'from_affiliator_id', 'to_affiliator_id'], 'unique_hierarchy_setting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_hierarchy_settings', function (Blueprint $table) {
            $table->dropForeign(['from_affiliator_id']);
            $table->dropForeign(['to_affiliator_id']);
            $table->dropUnique('unique_hierarchy_setting');
            $table->dropColumn(['from_affiliator_id', 'to_affiliator_id']);
            
            // Restore old unique constraint
            $table->unique(['from_level', 'to_level']);
        });
    }
};
