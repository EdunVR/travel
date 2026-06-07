<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('job_targets')) {
            Schema::create('job_targets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->decimal('target_percent', 5, 2)->default(100);
                $table->decimal('realisasi_percent', 5, 2)->default(0);
                $table->string('period')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('job_grade_settings')) {
            Schema::create('job_grade_settings', function (Blueprint $table) {
                $table->id();
                $table->string('grade', 1);
                $table->decimal('min_percent', 5, 2);
                $table->decimal('max_percent', 5, 2);
                $table->string('label');
                $table->string('color')->default('gray');
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_targets');
        Schema::dropIfExists('job_grade_settings');
    }
};
