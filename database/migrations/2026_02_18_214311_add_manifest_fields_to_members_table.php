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
            // Pas Foto
            if (!Schema::hasColumn('member', 'pas_foto')) {
                $table->string('pas_foto')->nullable()->after('alamat');
            }
            
            // KTP Fields
            if (!Schema::hasColumn('member', 'ktp_foto')) {
                $table->string('ktp_foto')->nullable()->after('pas_foto');
            }
            if (!Schema::hasColumn('member', 'ktp_nik')) {
                $table->string('ktp_nik', 20)->nullable()->after('ktp_foto');
            }
            if (!Schema::hasColumn('member', 'ktp_nama')) {
                $table->string('ktp_nama')->nullable()->after('ktp_nik');
            }
            if (!Schema::hasColumn('member', 'ktp_tempat_lahir')) {
                $table->string('ktp_tempat_lahir')->nullable()->after('ktp_nama');
            }
            if (!Schema::hasColumn('member', 'ktp_tanggal_lahir')) {
                $table->date('ktp_tanggal_lahir')->nullable()->after('ktp_tempat_lahir');
            }
            if (!Schema::hasColumn('member', 'ktp_alamat')) {
                $table->text('ktp_alamat')->nullable()->after('ktp_tanggal_lahir');
            }
            
            // Passport Fields
            if (!Schema::hasColumn('member', 'passport_foto')) {
                $table->string('passport_foto')->nullable()->after('ktp_alamat');
            }
            if (!Schema::hasColumn('member', 'passport_nomor')) {
                $table->string('passport_nomor', 20)->nullable()->after('passport_foto');
            }
            if (!Schema::hasColumn('member', 'passport_nama')) {
                $table->string('passport_nama')->nullable()->after('passport_nomor');
            }
            if (!Schema::hasColumn('member', 'passport_tanggal_lahir')) {
                $table->date('passport_tanggal_lahir')->nullable()->after('passport_nama');
            }
            if (!Schema::hasColumn('member', 'passport_tanggal_kadaluarsa')) {
                $table->date('passport_tanggal_kadaluarsa')->nullable()->after('passport_tanggal_lahir');
            }
            if (!Schema::hasColumn('member', 'passport_kewarganegaraan')) {
                $table->string('passport_kewarganegaraan')->nullable()->after('passport_tanggal_kadaluarsa');
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
                'pas_foto',
                'ktp_foto',
                'ktp_nik',
                'ktp_nama',
                'ktp_tempat_lahir',
                'ktp_tanggal_lahir',
                'ktp_alamat',
                'passport_foto',
                'passport_nomor',
                'passport_nama',
                'passport_tanggal_lahir',
                'passport_tanggal_kadaluarsa',
                'passport_kewarganegaraan'
            ]);
        });
    }
};
