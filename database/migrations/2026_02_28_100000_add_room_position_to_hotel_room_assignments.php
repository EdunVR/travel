<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_room_assignments', function (Blueprint $table) {
            $table->string('room_position')->nullable()->after('notes')->comment('Posisi kamar (misal: Berdekatan dengan Room 2)');
        });
    }

    public function down(): void
    {
        Schema::table('hotel_room_assignments', function (Blueprint $table) {
            $table->dropColumn('room_position');
        });
    }
};
