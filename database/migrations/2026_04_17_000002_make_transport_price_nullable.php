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
        Schema::table('saudi_transports', function (Blueprint $table) {
            $table->decimal('price_per_person', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saudi_transports', function (Blueprint $table) {
            $table->decimal('price_per_person', 15, 2)->nullable(false)->default(0)->change();
        });
    }
};
