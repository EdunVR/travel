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
            // Visa fields
            if (!Schema::hasColumn('member', 'visa_foto')) {
                $table->string('visa_foto')->nullable()->after('passport_kewarganegaraan');
            }
            if (!Schema::hasColumn('member', 'visa_nomor')) {
                $table->string('visa_nomor')->nullable()->after('visa_foto');
            }
            if (!Schema::hasColumn('member', 'visa_tipe')) {
                $table->string('visa_tipe')->nullable()->after('visa_nomor');
            }
            if (!Schema::hasColumn('member', 'visa_tanggal_terbit')) {
                $table->date('visa_tanggal_terbit')->nullable()->after('visa_tipe');
            }
            if (!Schema::hasColumn('member', 'visa_tanggal_kadaluarsa')) {
                $table->date('visa_tanggal_kadaluarsa')->nullable()->after('visa_tanggal_terbit');
            }
            if (!Schema::hasColumn('member', 'visa_negara')) {
                $table->string('visa_negara')->nullable()->after('visa_tanggal_kadaluarsa');
            }
            
            // Ticket fields
            if (!Schema::hasColumn('member', 'tiket_foto')) {
                $table->string('tiket_foto')->nullable()->after('visa_negara');
            }
            if (!Schema::hasColumn('member', 'tiket_nomor')) {
                $table->string('tiket_nomor')->nullable()->after('tiket_foto');
            }
            if (!Schema::hasColumn('member', 'tiket_maskapai')) {
                $table->string('tiket_maskapai')->nullable()->after('tiket_nomor');
            }
            if (!Schema::hasColumn('member', 'tiket_rute')) {
                $table->string('tiket_rute')->nullable()->after('tiket_maskapai');
            }
            if (!Schema::hasColumn('member', 'tiket_tanggal_berangkat')) {
                $table->date('tiket_tanggal_berangkat')->nullable()->after('tiket_rute');
            }
            if (!Schema::hasColumn('member', 'tiket_tanggal_pulang')) {
                $table->date('tiket_tanggal_pulang')->nullable()->after('tiket_tanggal_berangkat');
            }
            
            // Insurance fields
            if (!Schema::hasColumn('member', 'asuransi_foto')) {
                $table->string('asuransi_foto')->nullable()->after('tiket_tanggal_pulang');
            }
            if (!Schema::hasColumn('member', 'asuransi_nomor_polis')) {
                $table->string('asuransi_nomor_polis')->nullable()->after('asuransi_foto');
            }
            if (!Schema::hasColumn('member', 'asuransi_provider')) {
                $table->string('asuransi_provider')->nullable()->after('asuransi_nomor_polis');
            }
            if (!Schema::hasColumn('member', 'asuransi_tanggal_mulai')) {
                $table->date('asuransi_tanggal_mulai')->nullable()->after('asuransi_provider');
            }
            if (!Schema::hasColumn('member', 'asuransi_tanggal_akhir')) {
                $table->date('asuransi_tanggal_akhir')->nullable()->after('asuransi_tanggal_mulai');
            }
            
            // Health Certificate fields
            if (!Schema::hasColumn('member', 'sertifikat_kesehatan_foto')) {
                $table->string('sertifikat_kesehatan_foto')->nullable()->after('asuransi_tanggal_akhir');
            }
            if (!Schema::hasColumn('member', 'sertifikat_kesehatan_nomor')) {
                $table->string('sertifikat_kesehatan_nomor')->nullable()->after('sertifikat_kesehatan_foto');
            }
            if (!Schema::hasColumn('member', 'sertifikat_kesehatan_jenis')) {
                $table->string('sertifikat_kesehatan_jenis')->nullable()->after('sertifikat_kesehatan_nomor');
            }
            if (!Schema::hasColumn('member', 'sertifikat_kesehatan_tanggal_terbit')) {
                $table->date('sertifikat_kesehatan_tanggal_terbit')->nullable()->after('sertifikat_kesehatan_jenis');
            }
            if (!Schema::hasColumn('member', 'sertifikat_kesehatan_tanggal_kadaluarsa')) {
                $table->date('sertifikat_kesehatan_tanggal_kadaluarsa')->nullable()->after('sertifikat_kesehatan_tanggal_terbit');
            }
            if (!Schema::hasColumn('member', 'sertifikat_kesehatan_penerbit')) {
                $table->string('sertifikat_kesehatan_penerbit')->nullable()->after('sertifikat_kesehatan_tanggal_kadaluarsa');
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
                'visa_foto',
                'visa_nomor',
                'visa_tipe',
                'visa_tanggal_terbit',
                'visa_tanggal_kadaluarsa',
                'visa_negara',
                'tiket_foto',
                'tiket_nomor',
                'tiket_maskapai',
                'tiket_rute',
                'tiket_tanggal_berangkat',
                'tiket_tanggal_pulang',
                'asuransi_foto',
                'asuransi_nomor_polis',
                'asuransi_provider',
                'asuransi_tanggal_mulai',
                'asuransi_tanggal_akhir',
                'sertifikat_kesehatan_foto',
                'sertifikat_kesehatan_nomor',
                'sertifikat_kesehatan_jenis',
                'sertifikat_kesehatan_tanggal_terbit',
                'sertifikat_kesehatan_tanggal_kadaluarsa',
                'sertifikat_kesehatan_penerbit',
            ]);
        });
    }
};
