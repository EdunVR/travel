<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Tambah kolom jenjang ke affiliators
        Schema::table('affiliators', function (Blueprint $table) {
            $table->unsignedBigInteger('upline_master_id')->nullable()->after('partnership_program_id'); // cabang (HM Master)
            $table->unsignedBigInteger('upline_leader_id')->nullable()->after('upline_master_id');      // leader (HM Leader)
            $table->unsignedBigInteger('upline_partner_id')->nullable()->after('upline_leader_id');     // partner (HM Partner)
        });

        // 2. Tambah kolom termin ke affiliate_referrals
        Schema::table('affiliate_referrals', function (Blueprint $table) {
            $table->enum('termin', ['termin_1', 'termin_2'])->nullable()->after('status');
            $table->decimal('termin_1_amount', 15, 2)->default(0)->after('termin');
            $table->decimal('termin_2_amount', 15, 2)->default(0)->after('termin_1_amount');
            $table->timestamp('termin_1_paid_at')->nullable()->after('termin_2_amount');
            $table->timestamp('termin_2_paid_at')->nullable()->after('termin_1_paid_at');
            $table->boolean('termin_1_released')->default(false)->after('termin_2_paid_at');
            $table->boolean('termin_2_released')->default(false)->after('termin_1_released');
        });

        // 3. Tabel distribusi fee ke upline
        Schema::create('affiliate_fee_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('affiliate_referrals')->onDelete('cascade');
            $table->foreignId('from_affiliator_id')->constrained('affiliators')->onDelete('cascade'); // yang generate penjualan
            $table->foreignId('to_affiliator_id')->constrained('affiliators')->onDelete('cascade');   // yang menerima fee
            $table->string('level_type'); // 'partner', 'leader', 'master'
            $table->decimal('amount', 15, 2);
            $table->decimal('percentage', 5, 2); // persentase dari komisi dasar
            $table->enum('termin', ['termin_1', 'termin_2']);
            $table->enum('status', ['pending', 'released', 'paid'])->default('pending');
            $table->timestamp('released_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['to_affiliator_id', 'status']);
            $table->index('referral_id');
        });

        // 4. Tabel setting persentase fee antar jenjang
        Schema::create('affiliate_hierarchy_settings', function (Blueprint $table) {
            $table->id();
            $table->string('from_level'); // level yang generate penjualan: seller, partner, leader
            $table->string('to_level');   // level yang menerima: partner, leader, master
            $table->decimal('percentage', 5, 2)->default(0); // % dari komisi dasar
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['from_level', 'to_level']);
        });

        // Seed default hierarchy settings
        DB::table('affiliate_hierarchy_settings')->insert([
            // Seller -> upline
            ['from_level' => 'hm-seller', 'to_level' => 'hm-partner', 'percentage' => 10.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['from_level' => 'hm-seller', 'to_level' => 'hm-leader',  'percentage' => 5.00,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['from_level' => 'hm-seller', 'to_level' => 'hm-master',  'percentage' => 3.00,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            // Partner -> upline
            ['from_level' => 'hm-partner', 'to_level' => 'hm-leader', 'percentage' => 5.00,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['from_level' => 'hm-partner', 'to_level' => 'hm-master', 'percentage' => 3.00,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            // Leader -> upline
            ['from_level' => 'hm-leader', 'to_level' => 'hm-master',  'percentage' => 3.00,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('affiliate_hierarchy_settings');
        Schema::dropIfExists('affiliate_fee_distributions');

        Schema::table('affiliate_referrals', function (Blueprint $table) {
            $table->dropColumn(['termin', 'termin_1_amount', 'termin_2_amount', 'termin_1_paid_at', 'termin_2_paid_at', 'termin_1_released', 'termin_2_released']);
        });

        Schema::table('affiliators', function (Blueprint $table) {
            $table->dropColumn(['upline_master_id', 'upline_leader_id', 'upline_partner_id']);
        });
    }
};
