<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saudi_transports', function (Blueprint $table) {
            $table->id();
            $table->string('transport_code')->unique();
            $table->string('transport_name');
            $table->enum('transport_type', ['kereta_cepat', 'bus', 'lainnya'])->default('bus');
            $table->string('route_from')->nullable();
            $table->string('route_to')->nullable();
            $table->string('operator')->nullable();
            $table->decimal('price_per_person', 15, 2)->default(0);
            $table->string('seller_name')->nullable();
            $table->string('seller_phone')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('id_outlet')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saudi_transports');
    }
};
