<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keberangkatan_visas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_keberangkatan');
            $table->string('visa_type')->default('umrah'); // umrah, hajj, ziarah
            $table->string('seller_name')->nullable();
            $table->string('seller_phone')->nullable();
            $table->decimal('price_per_person', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, processing, ready, distributed
            $table->date('submission_date')->nullable();
            $table->date('ready_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('id_keberangkatan')->references('id')->on('keberangkatan')->onDelete('cascade');
            $table->index('id_keberangkatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keberangkatan_visas');
    }
};
