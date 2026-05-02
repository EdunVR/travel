<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            [
                'team_code' => 'administration',
                'team_name' => 'Administration Team',
                'description' => 'Handles document processing, visa management, and administrative tasks',
                'responsibilities' => [
                    'Form checking',
                    'Data input',
                    'Passport management',
                    'Visa management',
                    'Ticket management',
                    'Insurance management',
                    'Manifest creation',
                    'Siskopatuh reporting',
                    'Roomlist creation'
                ],
                'is_active' => true
            ],
            [
                'team_code' => 'customer_service',
                'team_name' => 'Customer Service Team',
                'description' => 'Manages customer communication and service delivery',
                'responsibilities' => [
                    'CS confirmation',
                    'Product information',
                    'Link/form submission',
                    'Invoice/receipt management',
                    'Data linking',
                    'Customer follow-up',
                    'Deal closing'
                ],
                'is_active' => true
            ],
            [
                'team_code' => 'finance',
                'team_name' => 'Finance Team',
                'description' => 'Handles financial operations and reporting',
                'responsibilities' => [
                    'Invoice creation',
                    'Down payment tracking',
                    'Receipt management',
                    'Payment completion follow-up',
                    'Departure RAB',
                    'Financial reports',
                    'HPP calculation'
                ],
                'is_active' => true
            ],
            [
                'team_code' => 'media',
                'team_name' => 'Media Team',
                'description' => 'Manages marketing materials and social media',
                'responsibilities' => [
                    'Timeline management',
                    'Deadline tracking',
                    'Testimonial collection',
                    'Informative flyer creation',
                    'Promotional video production',
                    'Social media management',
                    'Package information design'
                ],
                'is_active' => true
            ],
            [
                'team_code' => 'logistics',
                'team_name' => 'Logistics Team',
                'description' => 'Manages equipment and supplies for departures',
                'responsibilities' => [
                    'Equipment follow-up',
                    'Shipping deadline tracking',
                    'Stock updates',
                    'Packing list preparation',
                    'Supplier coordination'
                ],
                'is_active' => true
            ]
        ];

        foreach ($teams as $team) {
            Team::updateOrCreate(
                ['team_code' => $team['team_code']],
                $team
            );
        }
    }
}
