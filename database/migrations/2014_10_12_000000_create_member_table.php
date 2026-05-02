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
        Schema::create('member', function (Blueprint $table) {
            $table->id('id_member');
            
            // Basic Information
            $table->string('nama');
            $table->string('nama_perusahaan')->nullable();
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->string('pas_foto')->nullable();
            $table->unsignedBigInteger('id_tipe')->nullable();
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->string('kode_member')->nullable()->unique();
            
            // KTP Information
            $table->string('ktp_foto')->nullable();
            $table->string('ktp_nik', 16)->nullable();
            $table->string('ktp_nama')->nullable();
            $table->string('ktp_tempat_lahir')->nullable();
            $table->date('ktp_tanggal_lahir')->nullable();
            $table->text('ktp_alamat')->nullable();
            
            // Passport Information
            $table->string('passport_foto')->nullable();
            $table->string('passport_nomor')->nullable();
            $table->string('passport_nama')->nullable();
            $table->date('passport_tanggal_lahir')->nullable();
            $table->date('passport_tanggal_kadaluarsa')->nullable();
            $table->string('passport_kewarganegaraan')->nullable();
            
            // Visa Information
            $table->string('visa_foto')->nullable();
            $table->string('visa_nomor')->nullable();
            $table->string('visa_tipe')->nullable();
            $table->date('visa_tanggal_terbit')->nullable();
            $table->date('visa_tanggal_kadaluarsa')->nullable();
            $table->string('visa_negara')->nullable();
            
            // Ticket Information
            $table->string('tiket_foto')->nullable();
            $table->string('tiket_nomor')->nullable();
            $table->string('tiket_maskapai')->nullable();
            $table->string('tiket_rute')->nullable();
            $table->date('tiket_tanggal_berangkat')->nullable();
            $table->date('tiket_tanggal_pulang')->nullable();
            
            // Insurance Information
            $table->string('asuransi_foto')->nullable();
            $table->string('asuransi_nomor_polis')->nullable();
            $table->string('asuransi_provider')->nullable();
            $table->date('asuransi_tanggal_mulai')->nullable();
            $table->date('asuransi_tanggal_akhir')->nullable();
            
            // Health Certificate Information
            $table->string('sertifikat_kesehatan_foto')->nullable();
            $table->string('sertifikat_kesehatan_nomor')->nullable();
            $table->string('sertifikat_kesehatan_jenis')->nullable();
            $table->date('sertifikat_kesehatan_tanggal_terbit')->nullable();
            $table->date('sertifikat_kesehatan_tanggal_kadaluarsa')->nullable();
            $table->string('sertifikat_kesehatan_penerbit')->nullable();
            
            // Jamaah-specific fields
            $table->boolean('is_jamaah')->default(false);
            $table->enum('jamaah_type', ['umrah', 'hajj', 'umrah_plus'])->nullable();
            $table->string('mahram_name')->nullable();
            $table->string('mahram_relationship')->nullable();
            $table->string('mahram_phone')->nullable();
            $table->string('mahram_ktp_nik', 16)->nullable();
            $table->text('health_conditions')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('room_preference')->nullable();
            $table->text('special_requests')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->json('family_members')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('nama');
            $table->index('telepon');
            $table->index('ktp_nik');
            $table->index('passport_nomor');
            $table->index('id_tipe');
            $table->index('id_outlet');
            $table->index('is_jamaah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member');
    }
};
