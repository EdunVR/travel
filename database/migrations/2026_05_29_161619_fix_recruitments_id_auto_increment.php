<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix: id column must be auto-increment primary key
        DB::statement('ALTER TABLE recruitments ADD PRIMARY KEY (id)');
        DB::statement('ALTER TABLE recruitments MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE recruitments MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE recruitments DROP PRIMARY KEY');
    }
};
