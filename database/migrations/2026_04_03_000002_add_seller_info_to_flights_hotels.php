<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            if (!Schema::hasColumn('flights', 'seller_name')) {
                $table->string('seller_name')->nullable()->after('price');
            }
            if (!Schema::hasColumn('flights', 'seller_phone')) {
                $table->string('seller_phone')->nullable()->after('seller_name');
            }
        });

        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'seller_name')) {
                $table->string('seller_name')->nullable()->after('id_outlet');
            }
            if (!Schema::hasColumn('hotels', 'seller_phone')) {
                $table->string('seller_phone')->nullable()->after('seller_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn(['seller_name', 'seller_phone']);
        });
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['seller_name', 'seller_phone']);
        });
    }
};
