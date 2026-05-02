<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixMigrationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if migration already exists
        $exists = DB::table('migrations')
            ->where('migration', '2026_02_21_000018_create_hotel_room_assignments_table')
            ->exists();

        if (!$exists) {
            // Get the latest batch number
            $latestBatch = DB::table('migrations')->max('batch') ?? 1;

            // Insert the migration record
            DB::table('migrations')->insert([
                'migration' => '2026_02_21_000018_create_hotel_room_assignments_table',
                'batch' => $latestBatch
            ]);

            $this->command->info('Migration status fixed: 2026_02_21_000018_create_hotel_room_assignments_table');
        } else {
            $this->command->info('Migration already exists in migrations table.');
        }
    }
}
