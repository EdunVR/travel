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
            // Jamaah identification
            if (!Schema::hasColumn('member', 'is_jamaah')) {
                $table->boolean('is_jamaah')->default(false)->after('kode_member');
            }
            if (!Schema::hasColumn('member', 'jamaah_type')) {
                $table->enum('jamaah_type', ['hajj', 'umrah', 'umrah_plus'])->nullable()->after('is_jamaah');
            }
            
            // Mahram information
            if (!Schema::hasColumn('member', 'mahram_name')) {
                $table->string('mahram_name')->nullable()->after('jamaah_type');
            }
            if (!Schema::hasColumn('member', 'mahram_relationship')) {
                $table->string('mahram_relationship')->nullable()->after('mahram_name');
            }
            if (!Schema::hasColumn('member', 'mahram_phone')) {
                $table->string('mahram_phone')->nullable()->after('mahram_relationship');
            }
            
            // Health and emergency information
            if (!Schema::hasColumn('member', 'health_conditions')) {
                $table->text('health_conditions')->nullable()->after('mahram_phone');
            }
            if (!Schema::hasColumn('member', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('health_conditions');
            }
            if (!Schema::hasColumn('member', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
            
            // Preferences
            if (!Schema::hasColumn('member', 'room_preference')) {
                $table->string('room_preference')->nullable()->after('emergency_contact_phone');
            }
            if (!Schema::hasColumn('member', 'special_requests')) {
                $table->text('special_requests')->nullable()->after('room_preference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member', function (Blueprint $table) {
            $table->dropColumn([
                'is_jamaah',
                'jamaah_type',
                'mahram_name',
                'mahram_relationship',
                'mahram_phone',
                'health_conditions',
                'emergency_contact_name',
                'emergency_contact_phone',
                'room_preference',
                'special_requests'
            ]);
        });
    }
};
