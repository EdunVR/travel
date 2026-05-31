<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Release termin 2 setiap hari jam 00:01
        $schedule->command('affiliate:release-termin2')
                 ->dailyAt('00:01')
                 ->withoutOverlapping();

        // Payment reminders - check every minute
        $schedule->command('payment:send-reminders')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
