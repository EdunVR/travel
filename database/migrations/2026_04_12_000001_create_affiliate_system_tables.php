<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel Affiliator
        Schema::create('affiliators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Tanpa foreign key constraint
            $table->string('phone_number')->unique(); // Nomor HP sebagai slug referral
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->decimal('total_earnings', 15, 2)->default(0);
            $table->decimal('available_balance', 15, 2)->default(0);
            $table->decimal('pending_balance', 15, 2)->default(0);
            $table->integer('total_clicks')->default(0);
            $table->integer('total_sales')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('phone_number');
            $table->index('status');
        });

        // Tabel Klik Referral (PPC - Pay Per Click)
        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliator_id')->constrained('affiliators')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('travel_packages')->onDelete('set null');
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

        // Tabel Transaksi Referral (Sales)
        Schema::create('affiliate_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliator_id')->constrained('affiliators')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('jamaah_bookings')->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained('travel_packages')->onDelete('set null');
            $table->string('order_reference')->nullable();
            $table->decimal('order_amount', 15, 2);
            $table->decimal('commission_amount', 15, 2);
            $table->enum('commission_type', ['percentage', 'flat'])->default('percentage');
            $table->decimal('commission_rate', 10, 2)->nullable(); // Persentase atau nominal
            $table->enum('status', ['pending', 'verified', 'rejected', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('order_date');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['affiliator_id', 'status']);
            $table->index('booking_id');
        });

        // Tabel Komisi per Paket
        Schema::create('affiliate_package_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('travel_packages')->onDelete('cascade');
            $table->decimal('click_commission', 10, 2)->default(0); // Komisi per klik
            $table->enum('sale_commission_type', ['percentage', 'flat'])->default('percentage');
            $table->decimal('sale_commission_value', 10, 2); // Persentase atau nominal
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('package_id');
        });

        // Tabel Pembayaran ke Affiliator
        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliator_id')->constrained('affiliators')->onDelete('cascade');
            $table->string('payout_reference')->unique();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['bank_transfer', 'paypal', 'stripe', 'manual'])->default('bank_transfer');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('payment_details')->nullable(); // JSON untuk detail pembayaran
            $table->text('notes')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['affiliator_id', 'status']);
        });

        // Tabel Tracking Cookie
        Schema::create('affiliate_cookies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliator_id')->constrained('affiliators')->onDelete('cascade');
            $table->string('cookie_token')->unique();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index('cookie_token');
            $table->index('expires_at');
        });

        // Tabel Pengaturan Affiliate
        Schema::create('affiliate_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('affiliate_settings');
        Schema::dropIfExists('affiliate_cookies');
        Schema::dropIfExists('affiliate_payouts');
        Schema::dropIfExists('affiliate_package_commissions');
        Schema::dropIfExists('affiliate_referrals');
        Schema::dropIfExists('affiliate_clicks');
        Schema::dropIfExists('affiliators');
    }
};
