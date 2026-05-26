<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keberangkatan', function (Blueprint $table) {
            // JSON: array of {id, type:'main'|'family', booking_id, family_idx, group_label}
            // Stores the ordered manifest list with grouping info
            $table->json('manifest_order')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('keberangkatan', function (Blueprint $table) {
            $table->dropColumn('manifest_order');
        });
    }
};
