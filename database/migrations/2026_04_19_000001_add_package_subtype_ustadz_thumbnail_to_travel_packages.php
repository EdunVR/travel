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
            // Jenis paket yang lebih spesifik (umroh regular, umroh plus, umroh ramadhan, haji)
            $table->string('package_subtype')->nullable()->after('package_type');
            
            // Nama ustadz pendamping
            $table->string('ustadz_name')->nullable()->after('description');
            
            // Setting thumbnail crop (JSON: {x, y, width, height, zoom})
            $table->text('thumbnail_crop_settings')->nullable()->after('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_packages', function (Blueprint $table) {
            $table->dropColumn(['package_subtype', 'ustadz_name', 'thumbnail_crop_settings']);
        });
    }
};
