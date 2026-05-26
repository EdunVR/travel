<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member', function (Blueprint $table) {
            $table->string('passport_title', 20)->nullable()->after('passport_kewarganegaraan');
            $table->string('passport_gender', 10)->nullable()->after('passport_title');
            $table->date('passport_tanggal_terbit')->nullable()->after('passport_gender');
            $table->string('passport_kantor_terbit', 255)->nullable()->after('passport_tanggal_terbit');
            $table->string('passport_tempat_lahir', 255)->nullable()->after('passport_kantor_terbit');
        });
    }

    public function down(): void
    {
        Schema::table('member', function (Blueprint $table) {
            $table->dropColumn([
                'passport_title',
                'passport_gender',
                'passport_tanggal_terbit',
                'passport_kantor_terbit',
                'passport_tempat_lahir',
            ]);
        });
    }
};
