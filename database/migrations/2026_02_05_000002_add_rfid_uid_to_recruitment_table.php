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
        if (Schema::hasTable('recruitments')) {
            Schema::table('recruitments', function (Blueprint $table) {
                if (!Schema::hasColumn('recruitments', 'rfid_uid')) {
                    $table->string('rfid_uid')->nullable()->unique()->after('fingerprint_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            if (Schema::hasColumn('recruitments', 'rfid_uid')) {
                $table->dropColumn('rfid_uid');
            }
        });
    }
};