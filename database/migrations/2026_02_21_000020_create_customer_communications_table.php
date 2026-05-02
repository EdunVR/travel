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
        Schema::create('customer_communications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_travel_package')->nullable();
            $table->unsignedBigInteger('id_member');
            $table->enum('communication_method', ['phone_call', 'whatsapp', 'email', 'in_person', 'other'])->default('phone_call');
            $table->timestamp('communication_date');
            $table->text('notes')->nullable();
            $table->enum('follow_up_status', ['pending', 'contacted', 'responded', 'no_response'])->default('pending');
            $table->date('next_follow_up_date')->nullable();
            $table->unsignedBigInteger('contacted_by');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_travel_package')->references('id')->on('travel_packages')->onDelete('set null');
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('cascade');
            
            // Indexes
            $table->index('id_travel_package');
            $table->index('id_member');
            $table->index('communication_date');
            $table->index('follow_up_status');
            $table->index('next_follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_communications');
    }
};
