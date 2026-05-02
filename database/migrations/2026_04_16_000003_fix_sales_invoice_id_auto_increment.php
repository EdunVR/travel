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
        // Fix the id_sales_invoice column to be auto-increment
        DB::statement('ALTER TABLE sales_invoice MODIFY id_sales_invoice BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove auto-increment (not recommended, but for rollback)
        DB::statement('ALTER TABLE sales_invoice MODIFY id_sales_invoice BIGINT UNSIGNED NOT NULL');
    }
};
