<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('id_saudi_transport')->nullable()->after('id_hotel_room_type_madinah');
            $table->foreign('id_saudi_transport')->references('id')->on('saudi_transports')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->dropForeign(['id_saudi_transport']);
            $table->dropColumn('id_saudi_transport');
        });
    }
};
