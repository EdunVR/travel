<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;

class RecalculateAttendance extends Command
{
    protected $signature = 'attendance:recalculate {--date= : Specific date to recalculate (Y-m-d)} {--all : Recalculate all records}';
    protected $description = 'Recalculate attendance metrics (hours worked, late minutes, early minutes, overtime)';

    public function handle()
    {
        $this->info('Starting attendance recalculation...');

        $query = Attendance::query();

        if ($this->option('date')) {
            $query->whereDate('date', $this->option('date'));
            $this->info('Recalculating for date: ' . $this->option('date'));
        } elseif ($this->option('all')) {
            $this->info('Recalculating ALL attendance records...');
        } else {
            // Default: recalculate last 30 days
            $query->where('date', '>=', now()->subDays(30));
            $this->info('Recalculating last 30 days...');
        }

        $attendances = $query->get();
        $total = $attendances->count();

        if ($total === 0) {
            $this->warn('No attendance records found.');
            return 0;
        }

        $this->info("Found {$total} records to recalculate.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;
        foreach ($attendances as $attendance) {
            try {
                $attendance->autoCalculate();
                $attendance->save();
                $updated++;
            } catch (\Exception $e) {
                $this->error("\nError recalculating attendance ID {$attendance->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Successfully recalculated {$updated} out of {$total} records.");

        return 0;
    }
}
