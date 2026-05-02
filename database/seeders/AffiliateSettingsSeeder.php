<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AffiliateSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'affiliate_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable/disable affiliate system'
            ],
            [
                'key' => 'cookie_lifetime',
                'value' => '259200', // 3 hari dalam detik
                'type' => 'integer',
                'description' => 'Cookie lifetime in seconds (3600=1 hour, 28800=8 hours, 259200=3 days)'
            ],
            [
                'key' => 'default_click_commission',
                'value' => '1000',
                'type' => 'integer',
                'description' => 'Default commission per click (in Rupiah)'
            ],
            [
                'key' => 'default_sale_commission_type',
                'value' => 'percentage',
                'type' => 'string',
                'description' => 'Default sale commission type (percentage or flat)'
            ],
            [
                'key' => 'default_sale_commission_value',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Default sale commission value (5 = 5% or Rp 5)'
            ],
            [
                'key' => 'minimum_payout',
                'value' => '100000',
                'type' => 'integer',
                'description' => 'Minimum payout amount in Rupiah'
            ],
            [
                'key' => 'auto_approve_affiliates',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Auto approve new affiliate registrations'
            ],
            [
                'key' => 'click_fraud_prevention',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable click fraud prevention (same IP within 24 hours)'
            ],
            [
                'key' => 'referral_url_format',
                'value' => 'phone',
                'type' => 'string',
                'description' => 'Referral URL format (phone, username, or id)'
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('affiliate_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
    }
}
