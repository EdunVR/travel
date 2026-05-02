<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->text('family_members_booking')->nullable()->after('closing_source');
        });
    }

    public function down()
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->dropColumn('family_members_booking');
        });
    }
};
