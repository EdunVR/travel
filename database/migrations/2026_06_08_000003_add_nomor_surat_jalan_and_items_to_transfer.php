<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan tabel transfer_request_items dan kolom nomor_surat_jalan
     * ke permintaan_pengiriman agar satu request bisa berisi banyak item.
     */
    public function up(): void
    {
        // Tambah kolom nomor_surat_jalan ke permintaan_pengiriman jika belum ada
        if (!Schema::hasColumn('permintaan_pengiriman', 'nomor_surat_jalan')) {
            Schema::table('permintaan_pengiriman', function (Blueprint $table) {
                $table->string('nomor_surat_jalan')->nullable()->after('status');
            });
        }

        // Tabel detail item per request transfer
        if (!Schema::hasTable('transfer_request_items')) {
            Schema::create('transfer_request_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('transfer_request_id'); // FK ke permintaan_pengiriman.id
                $table->enum('item_type', ['produk', 'bahan', 'inventori']);
                $table->unsignedBigInteger('item_id');             // id asli sesuai item_type
                $table->string('item_name');
                $table->integer('jumlah')->default(1);
                $table->string('unit')->nullable();
                $table->timestamps();

                $table->foreign('transfer_request_id')
                      ->references('id')->on('permintaan_pengiriman')
                      ->onDelete('cascade');
                $table->index(['transfer_request_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_request_items');
        Schema::table('permintaan_pengiriman', function (Blueprint $table) {
            $table->dropColumnIfExists('nomor_surat_jalan');
        });
    }
};
