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
        Schema::create('jamaah_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->unsignedBigInteger('id_travel_package');
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_keberangkatan')->nullable();
            $table->date('booking_date');
            $table->enum('status', ['pending', 'confirmed', 'paid', 'departed', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 15, 2);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2);
            $table->unsignedBigInteger('id_invoice')->nullable();
            $table->unsignedBigInteger('id_outlet')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_travel_package')->references('id')->on('travel_packages')->onDelete('cascade');
            $table->foreign('id_member')->references('id_member')->on('member')->onDelete('cascade');
            $table->foreign('id_keberangkatan')->references('id')->on('keberangkatan')->onDelete('set null');
            
            // Indexes
            $table->index('booking_code');
            $table->index('id_travel_package');
            $table->index('id_member');
            $table->index('id_keberangkatan');
            $table->index('status');
            $table->index('payment_status');
            $table->index('id_outlet');
            $table->index('booking_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jamaah_bookings');
    }
};
