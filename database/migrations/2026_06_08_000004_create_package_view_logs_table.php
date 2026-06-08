<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel untuk logging view detail per paket per hari.
     * Digunakan untuk menampilkan grafik views harian di halaman analytics katalog.
     */
    public function up(): void
    {
        if (!Schema::hasTable('package_view_logs')) {
            Schema::create('package_view_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('travel_package_id');
                $table->date('viewed_date');
                $table->unsignedInteger('view_count')->default(1);
                $table->string('referrer', 500)->nullable();   // dari mana (URL referrer)
                $table->string('source', 50)->nullable();       // admin / public / api
                $table->timestamps();

                $table->foreign('travel_package_id')
                      ->references('id')->on('travel_packages')
                      ->onDelete('cascade');

                // Unique per package per date per source untuk aggregasi harian
                $table->index(['travel_package_id', 'viewed_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('package_view_logs');
    }
};
