<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixMigrationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration:fix-status {migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark a migration as run in the migrations table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migrationName = $this->argument('migration');

        // Check if migration already exists
        $exists = DB::table('migrations')
            ->where('migration', $migrationName)
            ->exists();

        if ($exists) {
            $this->info("Migration '{$migrationName}' already exists in migrations table.");
            return 0;
        }

        // Get the latest batch number
        $latestBatch = DB::table('migrations')->max('batch') ?? 1;

        // Insert the migration record
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => $latestBatch
        ]);

        $this->info("Migration '{$migrationName}' has been marked as run in batch {$latestBatch}.");
        return 0;
    }
}
