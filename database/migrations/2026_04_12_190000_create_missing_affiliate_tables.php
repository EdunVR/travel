<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel Klik Referral (PPC - Pay Per Click)
        if (!Schema::hasTable('affiliate_clicks')) {
            Schema::create('affiliate_clicks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliator_id');
                $table->unsignedBigInteger('package_id')->nullable();
                $table->string('ip_address', 45);
                $table->text('user_agent')->nullable();
                $table->string('referrer_url')->nullable();
                $table->string('landing_page')->nullable();
                $table->decimal('commission_amount', 10, 2)->default(0);
                $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
                $table->timestamp('clicked_at');
                $table->timestamps();
                
                $table->index(['affiliator_id', 'clicked_at']);
                $table->index(['ip_address', 'clicked_at']);
            });
        }

        // Tabel Transaksi Referral (Sales)
        if (!Schema::hasTable('affiliate_referrals')) {
            Schema::create('affiliate_referrals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliator_id');
                $table->unsignedBigInteger('booking_id')->nullable();
                $table->unsignedBigInteger('package_id')->nullable();
                $table->string('order_reference')->nullable();
                $table->decimal('order_amount', 15, 2);
                $table->decimal('commission_amount', 15, 2);
                $table->enum('commission_type', ['percentage', 'flat'])->default('percentage');
                $table->decimal('commission_rate', 10, 2)->nullable();
                $table->enum('status', ['pending', 'verified', 'rejected', 'paid'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamp('order_date');
                $table->timestamp('verified_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
                
                $table->index(['affiliator_id', 'status']);
                $table->index('booking_id');
            });
        }

        // Tabel Komisi per Paket
        if (!Schema::hasTable('affiliate_package_commissions')) {
            Schema::create('affiliate_package_commissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('package_id');
                $table->unsignedBigInteger('affiliator_id')->nullable();
                $table->decimal('click_commission', 10, 2)->default(0);
                $table->enum('sale_commission_type', ['percentage', 'flat'])->default('percentage');
                $table->decimal('sale_commission_value', 10, 2);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index('package_id');
                $table->index('affiliator_id');
            });
        }

        // Tabel Pembayaran ke Affiliator
        if (!Schema::hasTable('affiliate_payouts')) {
            Schema::create('affiliate_payouts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliator_id');
                $table->string('payout_reference')->unique();
                $table->decimal('amount', 15, 2);
                $table->enum('payment_method', ['bank_transfer', 'paypal', 'stripe', 'manual'])->default('bank_transfer');
                $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
                $table->text('payment_details')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('requested_at');
                $table->timestamp('processed_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                
                $table->index(['affiliator_id', 'status']);
            });
        }

        // Tabel Tracking Cookie
        if (!Schema::hasTable('affiliate_cookies')) {
            Schema::create('affiliate_cookies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliator_id');
                $table->string('cookie_token')->unique();
                $table->string('ip_address', 45);
                $table->text('user_agent')->nullable();
                $table->timestamp('expires_at');
                $table->timestamps();
                
                $table->index('cookie_token');
                $table->index('expires_at');
            });
        }

        // Tabel Pengaturan Affiliate
        if (!Schema::hasTable('affiliate_settings')) {
            Schema::create('affiliate_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('type')->default('string');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('affiliate_settings');
        Schema::dropIfExists('affiliate_cookies');
        Schema::dropIfExists('affiliate_payouts');
        Schema::dropIfExists('affiliate_package_commissions');
        Schema::dropIfExists('affiliate_referrals');
        Schema::dropIfExists('affiliate_clicks');
    }
};
