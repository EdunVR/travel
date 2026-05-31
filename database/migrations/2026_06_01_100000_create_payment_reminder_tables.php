<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Settings table for reminder configuration
        Schema::create('payment_reminder_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('payment_reminder_settings')->insert([
            ['key' => 'reminder_days', 'value' => '30,15,7', 'description' => 'Hari sebelum keberangkatan untuk mengirim pengingat (pisahkan koma)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'start_time', 'value' => '09:00', 'description' => 'Jam mulai pengiriman pengingat (format 24 jam)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'interval_minutes', 'value' => '15', 'description' => 'Selisih waktu antar pengiriman (menit)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'is_active', 'value' => '1', 'description' => 'Aktifkan/nonaktifkan pengingat otomatis', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'message_template', 'value' => "Assalamu'alaikum {nama},\n\nIni adalah pengingat pembayaran untuk paket *{paket}* dengan kode booking *{kode_booking}*.\n\nSisa pembayaran: *Rp {sisa_bayar}*\nTanggal keberangkatan: *{tgl_berangkat}*\nSisa waktu: *{sisa_hari} hari lagi*\n\nSilakan lakukan pembayaran sebelum tanggal keberangkatan.\n\nTerima kasih.\n— HM Tour & Travel", 'description' => 'Template pesan WhatsApp', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Logs table for tracking sent reminders
        Schema::create('payment_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jamaah_booking');
            $table->unsignedBigInteger('id_member')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('reminder_type', 20)->comment('H-30, H-15, H-7, etc');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'skipped'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['id_jamaah_booking', 'reminder_type']);
            $table->index(['status', 'scheduled_at']);
        });

        // Cron job execution log
        Schema::create('cron_job_logs', function (Blueprint $table) {
            $table->id();
            $table->string('command');
            $table->enum('status', ['running', 'success', 'failed'])->default('running');
            $table->integer('processed_count')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('skipped_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['command', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cron_job_logs');
        Schema::dropIfExists('payment_reminder_logs');
        Schema::dropIfExists('payment_reminder_settings');
    }
};
