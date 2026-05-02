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
        Schema::table('member', function (Blueprint $table) {
            // Add missing fields from requirements
            if (!Schema::hasColumn('member', 'mahram_ktp_nik')) {
                $table->string('mahram_ktp_nik', 16)->nullable()->after('mahram_phone');
            }
            if (!Schema::hasColumn('member', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            }
            if (!Schema::hasColumn('member', 'gender')) {
                $table->enum('gender', ['male', 'female'])->nullable()->after('special_requests');
            }
            
            // Add indexes for performance
            if (!Schema::hasColumn('member', 'is_jamaah')) {
                $table->index('is_jamaah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member', function (Blueprint $table) {
            $table->dropIndex(['is_jamaah']);
            $table->dropIndex(['jamaah_type']);
            
            $table->dropColumn([
                'mahram_ktp_nik',
                'emergency_contact_relationship',
                'gender'
            ]);
        });
    }
};
