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
        Schema::table('flights', function (Blueprint $table) {
            $table->string('flight_group_code')->nullable()->after('flight_number');
            $table->enum('flight_direction', ['departure', 'return'])->nullable()->after('flight_group_code');
            
            $table->index('flight_group_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropIndex(['flight_group_code']);
            $table->dropColumn(['flight_group_code', 'flight_direction']);
        });
    }
};
