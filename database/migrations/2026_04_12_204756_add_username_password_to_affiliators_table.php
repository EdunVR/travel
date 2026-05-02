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
        Schema::table('affiliators', function (Blueprint $table) {
            // Add username and password columns
            if (!Schema::hasColumn('affiliators', 'username')) {
                $table->string('username')->unique()->after('phone_number');
            }
            if (!Schema::hasColumn('affiliators', 'password')) {
                $table->string('password')->after('username');
            }
            if (!Schema::hasColumn('affiliators', 'photo')) {
                $table->string('photo')->nullable()->after('email');
            }
            if (!Schema::hasColumn('affiliators', 'partnership_program_id')) {
                $table->unsignedBigInteger('partnership_program_id')->nullable()->after('photo');
            }
            
            // Add indexes
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliators', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropColumn(['username', 'password', 'photo', 'partnership_program_id']);
        });
    }
};
