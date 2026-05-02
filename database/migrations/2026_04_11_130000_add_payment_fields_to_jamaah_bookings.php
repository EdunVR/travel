<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->string('payment_type')->nullable()->after('family_members_booking'); // 'full' or 'dp'
            $table->string('dp_option')->nullable()->after('payment_type'); // '25_percent' or '5_million'
        });
    }

    public function down()
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'dp_option']);
        });
    }
};
