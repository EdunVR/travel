<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keberangkatan_reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_keberangkatan');
            $table->string('reminder_type'); // hotel, tiket, visa, kereta_cepat
            $table->string('target_role')->default('owner'); // owner, admin
            $table->string('status')->default('pending'); // pending, sent, dismissed
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();

            $table->foreign('id_keberangkatan')->references('id')->on('keberangkatan')->onDelete('cascade');
            $table->index(['id_keberangkatan', 'reminder_type']);
            $table->index(['scheduled_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keberangkatan_reminders');
    }
};
