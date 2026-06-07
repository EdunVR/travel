<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rfid_settings')) {
            return;
        }

        Schema::create('rfid_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        // Insert default mode
        DB::table('rfid_settings')->insert([
            ['key' => 'mode', 'value' => 'attendance', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'detected_uid', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rfid_settings');
    }
};
