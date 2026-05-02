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
        Schema::create('jamaah_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jamaah_booking');
            $table->enum('document_type', ['passport', 'visa', 'ticket', 'insurance', 'health_certificate']);
            $table->string('document_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_jamaah_booking')->references('id')->on('jamaah_bookings')->onDelete('cascade');
            
            // Indexes
            $table->index('id_jamaah_booking');
            $table->index('document_type');
            $table->index('status');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jamaah_documents');
    }
};
