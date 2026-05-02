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
        Schema::create('affiliate_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_affiliator')->constrained('affiliators')->onDelete('cascade');
            $table->string('code', 50)->unique()->comment('Kode voucher unik');
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage')->comment('Tipe diskon: persentase atau nominal tetap');
            $table->decimal('discount_value', 15, 2)->comment('Nilai diskon (% atau Rp)');
            $table->decimal('max_discount', 15, 2)->nullable()->comment('Maksimal diskon (untuk percentage)');
            $table->decimal('min_transaction', 15, 2)->default(0)->comment('Minimal transaksi');
            $table->integer('usage_limit')->nullable()->comment('Batas penggunaan (null = unlimited)');
            $table->integer('usage_count')->default(0)->comment('Jumlah sudah digunakan');
            $table->date('valid_from')->nullable()->comment('Berlaku dari tanggal');
            $table->date('valid_until')->nullable()->comment('Berlaku sampai tanggal');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->text('description')->nullable()->comment('Deskripsi voucher');
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
            $table->index(['valid_from', 'valid_until']);
        });

        // Tabel untuk tracking penggunaan voucher
        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_voucher')->constrained('affiliate_vouchers')->onDelete('cascade');
            $table->foreignId('id_jamaah_booking')->constrained('jamaah_bookings')->onDelete('cascade');
            $table->decimal('discount_amount', 15, 2)->comment('Jumlah diskon yang diberikan');
            $table->decimal('original_amount', 15, 2)->comment('Harga asli sebelum diskon');
            $table->decimal('final_amount', 15, 2)->comment('Harga setelah diskon');
            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();
            
            $table->index('id_voucher');
            $table->index('id_jamaah_booking');
        });

        // Add voucher columns to jamaah_bookings
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->foreignId('id_voucher')->nullable()->after('id')->constrained('affiliate_vouchers')->onDelete('set null');
            $table->decimal('voucher_discount', 15, 2)->default(0)->after('id_voucher')->comment('Diskon dari voucher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->dropForeign(['id_voucher']);
            $table->dropColumn(['id_voucher', 'voucher_discount']);
        });
        
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('affiliate_vouchers');
    }
};
