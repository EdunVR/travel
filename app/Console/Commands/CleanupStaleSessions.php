<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupStaleSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up stale sessions to prevent redirect issues';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🧹 Starting session cleanup...');

        try {
            // Calculate cutoff time (sessions older than configured lifetime)
            $lifetimeMinutes = config('session.lifetime', 480);
            $cutoffTime = Carbon::now()->subMinutes($lifetimeMinutes);

            $this->info("Cleaning sessions older than: {$cutoffTime->format('Y-m-d H:i:s')}");

            // Count stale sessions
            $staleCount = DB::table('sessions')
                ->where('last_activity', '<', $cutoffTime->timestamp)
                ->count();

            if ($staleCount === 0) {
                $this->info('✅ No stale sessions found.');
                return 0;
            }

            $this->warn("Found {$staleCount} stale sessions.");

            // Ask for confirmation unless forced
            if (!$this->option('force') && !$this->confirm('Do you want to delete these stale sessions?')) {
                $this->info('Session cleanup cancelled.');
                return 0;
            }

            // Delete stale sessions
            $deletedCount = DB::table('sessions')
                ->where('last_activity', '<', $cutoffTime->timestamp)
                ->delete();

            $this->info("✅ Deleted {$deletedCount} stale sessions.");

            // Log the cleanup
            Log::info('🧹 [SESSION CLEANUP] Stale sessions cleaned up', [
                'deleted_count' => $deletedCount,
                'cutoff_time' => $cutoffTime->toDateTimeString(),
                'lifetime_minutes' => $lifetimeMinutes
            ]);

            // Additional cleanup for session files if using file driver
            if (config('session.driver') === 'file') {
                $this->cleanupSessionFiles($cutoffTime);
            }

            $this->info('🎉 Session cleanup completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('💥 Error during session cleanup: ' . $e->getMessage());
            Log::error('💥 [SESSION CLEANUP] Error occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Clean up session files if using file driver
     */
    private function cleanupSessionFiles(Carbon $cutoffTime)
    {
        $sessionPath = config('session.files');
        
        if (!is_dir($sessionPath)) {
            return;
        }

        $files = glob($sessionPath . '/sess_*');
        $deletedFiles = 0;

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime->timestamp) {
                if (unlink($file)) {
                    $deletedFiles++;
                }
            }
        }

        if ($deletedFiles > 0) {
            $this->info("🗂️ Deleted {$deletedFiles} stale session files.");
        }
    }
}