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
        Schema::table('hpp_calculations', function (Blueprint $table) {
            // JSON column untuk menyimpan custom components
            // Format: [{"id": "custom_123", "label": "Biaya X", "value": 100000, "payment_status": "hutang", "hutang_amount": 5000000}]
            $table->json('custom_components')->nullable()->after('contingency');
            
            // JSON column untuk payment status per component
            $table->json('component_payment_status')->nullable()->after('custom_components');
            
            // JSON column untuk hutang amount per component
            $table->json('component_hutang_amount')->nullable()->after('component_payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hpp_calculations', function (Blueprint $table) {
            $table->dropColumn(['custom_components', 'component_payment_status', 'component_hutang_amount']);
        });
    }
};
