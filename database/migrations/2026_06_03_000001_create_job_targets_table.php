<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');          // target user
            $table->string('title');                         // nama job / tugas
            $table->text('description')->nullable();         // deskripsi
            $table->decimal('target_percent', 5, 2)->default(100); // target realisasi %
            $table->decimal('realisasi_percent', 5, 2)->default(0); // realisasi aktual % (diisi user)
            $table->string('period')->nullable();            // periode e.g. "Juni 2026"
            $table->unsignedBigInteger('created_by');        // super admin yang buat
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('job_grade_settings', function (Blueprint $table) {
            $table->id();
            $table->string('grade', 1);          // A, B, C, D
            $table->decimal('min_percent', 5, 2); // batas bawah %
            $table->decimal('max_percent', 5, 2); // batas atas %
            $table->string('label');               // "Sangat Baik", dst
            $table->string('color')->default('gray'); // warna badge
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_targets');
        Schema::dropIfExists('job_grade_settings');
    }
};
