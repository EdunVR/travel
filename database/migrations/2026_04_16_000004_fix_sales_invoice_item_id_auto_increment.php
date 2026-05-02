<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix sales_invoice_item table - add AUTO_INCREMENT to id_sales_invoice_item
        DB::statement('ALTER TABLE `sales_invoice_item` MODIFY `id_sales_invoice_item` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove AUTO_INCREMENT from id_sales_invoice_item
        DB::statement('ALTER TABLE `sales_invoice_item` MODIFY `id_sales_invoice_item` INT UNSIGNED NOT NULL');
    }
};
