<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member', function (Blueprint $table) {
            // Dokumen manifest tambahan untuk jamaah utama
            $table->string('akta_lahir_foto')->nullable()->after('passport_kewarganegaraan');
            $table->string('kartu_keluarga_foto')->nullable()->after('akta_lahir_foto');
            $table->string('buku_nikah_foto')->nullable()->after('kartu_keluarga_foto');
            $table->string('vaksin_foto')->nullable()->after('buku_nikah_foto');
            $table->string('bpjs_foto')->nullable()->after('vaksin_foto');
        });
    }

    public function down(): void
    {
        Schema::table('member', function (Blueprint $table) {
            $table->dropColumn([
                'akta_lahir_foto',
                'kartu_keluarga_foto',
                'buku_nikah_foto',
                'vaksin_foto',
                'bpjs_foto',
            ]);
        });
    }
};
