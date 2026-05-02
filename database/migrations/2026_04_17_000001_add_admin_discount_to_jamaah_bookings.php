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
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->decimal('admin_discount', 15, 2)->default(0)->after('discount_amount');
            $table->text('admin_discount_notes')->nullable()->after('admin_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->dropColumn(['admin_discount', 'admin_discount_notes']);
        });
    }
};
