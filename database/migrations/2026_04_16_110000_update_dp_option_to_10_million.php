<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records from 5_million to 10_million
        DB::table('jamaah_bookings')
            ->where('dp_option', '5_million')
            ->update(['dp_option' => '10_million']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to 5_million
        DB::table('jamaah_bookings')
            ->where('dp_option', '10_million')
            ->update(['dp_option' => '5_million']);
    }
};
