<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: modify enum to add 'rejected'
        DB::statement("ALTER TABLE affiliate_fee_distributions MODIFY COLUMN status ENUM('pending','released','paid','rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE affiliate_fee_distributions MODIFY COLUMN status ENUM('pending','released','paid') NOT NULL DEFAULT 'pending'");
    }
};
