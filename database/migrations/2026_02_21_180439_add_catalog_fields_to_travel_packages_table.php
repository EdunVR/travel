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
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->text('inclusions')->nullable()->after('description');
            $table->string('image_path')->nullable()->after('inclusions');
            $table->integer('view_count')->default(0)->after('image_path');
            $table->integer('booking_count')->default(0)->after('view_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->dropColumn(['inclusions', 'image_path', 'view_count', 'booking_count']);
        });
    }
};
