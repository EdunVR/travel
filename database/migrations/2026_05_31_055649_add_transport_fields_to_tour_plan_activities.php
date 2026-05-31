<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_plan_activities', function (Blueprint $table) {
            $table->boolean('is_transport_info')->default(false)->after('order');
            $table->string('transport_from')->nullable()->after('is_transport_info');
            $table->string('transport_to')->nullable()->after('transport_from');
            $table->string('transport_remark')->nullable()->after('transport_to');
        });
    }

    public function down(): void
    {
        Schema::table('tour_plan_activities', function (Blueprint $table) {
            $table->dropColumn(['is_transport_info', 'transport_from', 'transport_to', 'transport_remark']);
        });
    }
};
