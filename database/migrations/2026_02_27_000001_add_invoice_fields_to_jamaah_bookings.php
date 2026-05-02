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
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            // Seller/Closing information
            if (!Schema::hasColumn('jamaah_bookings', 'closed_by_user_id')) {
                $table->unsignedBigInteger('closed_by_user_id')->nullable()->after('id_outlet');
            }
            if (!Schema::hasColumn('jamaah_bookings', 'closing_source')) {
                $table->enum('closing_source', ['kantor', 'alumni', 'digital_marketing', 'event'])->nullable()->after('closed_by_user_id');
            }
            
            // Room type preference
            if (!Schema::hasColumn('jamaah_bookings', 'room_type')) {
                $table->enum('room_type', ['quad', 'triple', 'double', 'single'])->nullable()->after('closing_source');
            }
            
            // Additional costs
            if (!Schema::hasColumn('jamaah_bookings', 'equipment_cost')) {
                $table->decimal('equipment_cost', 15, 2)->default(0)->after('room_type')->comment('Biaya perlengkapan tambahan');
            }
            if (!Schema::hasColumn('jamaah_bookings', 'upgrade_cost')) {
                $table->decimal('upgrade_cost', 15, 2)->default(0)->after('equipment_cost')->comment('Biaya upgrade');
            }
            if (!Schema::hasColumn('jamaah_bookings', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('upgrade_cost')->comment('Diskon');
            }
            
            // Notes for equipment and upgrade
            if (!Schema::hasColumn('jamaah_bookings', 'equipment_notes')) {
                $table->text('equipment_notes')->nullable()->after('discount_amount')->comment('Catatan perlengkapan tambahan');
            }
            if (!Schema::hasColumn('jamaah_bookings', 'upgrade_notes')) {
                $table->text('upgrade_notes')->nullable()->after('equipment_notes')->comment('Catatan upgrade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_bookings', function (Blueprint $table) {
            $table->dropForeign(['closed_by_user_id']);
            $table->dropColumn([
                'closed_by_user_id',
                'closing_source',
                'room_type',
                'equipment_cost',
                'upgrade_cost',
                'discount_amount',
                'equipment_notes',
                'upgrade_notes'
            ]);
        });
    }
};
