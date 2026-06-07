<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tasks')) {
            return;
        }

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);                    // judul task (NOT NULL)
            $table->text('description')->nullable();          // deskripsi task
            $table->date('due_date')->nullable();             // tanggal target penyelesaian
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium'); // tingkat urgensi
            $table->enum('status', ['todo', 'in_progress', 'review', 'done'])->default('todo'); // tahap pengerjaan
            $table->unsignedBigInteger('assigned_to')->nullable(); // FK ke users.id (penanggung jawab)
            $table->string('category', 100)->nullable();     // label kategori
            $table->text('attachment_notes')->nullable();     // catatan/lampiran
            $table->unsignedBigInteger('created_by');         // FK ke users.id (pembuat task)
            $table->timestamps();

            $table->foreign('assigned_to')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
