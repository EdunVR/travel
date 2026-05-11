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
        Schema::table('affiliators', function (Blueprint $table) {
            $table->unsignedBigInteger('recruited_by')->nullable()->after('id');
            $table->foreign('recruited_by')->references('id')->on('affiliators')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliators', function (Blueprint $table) {
            $table->dropForeign(['recruited_by']);
            $table->dropColumn('recruited_by');
        });
    }
};
