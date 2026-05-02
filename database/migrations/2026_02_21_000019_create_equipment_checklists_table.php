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
        Schema::create('equipment_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_keberangkatan');
            $table->string('equipment_name');
            $table->string('equipment_category')->nullable();
            $table->integer('quantity_needed')->unsigned();
            $table->integer('quantity_received')->unsigned()->default(0);
            $table->enum('status', ['not_ordered', 'ordered', 'received', 'packed', 'shipped'])->default('not_ordered');
            $table->string('supplier_name')->nullable();
            $table->date('order_date')->nullable();
            $table->date('shipping_deadline')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_keberangkatan')->references('id')->on('keberangkatan')->onDelete('cascade');
            
            // Indexes
            $table->index('id_keberangkatan');
            $table->index('status');
            $table->index('shipping_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_checklists');
    }
};
