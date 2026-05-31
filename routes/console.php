<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Release termin 2 fee affiliator setiap hari jam 07:00
Schedule::command('affiliate:release-termin2')->dailyAt('07:00');

// Payment reminders - cek setiap menit, logika jadwal di dalam command
Schedule::command('payment:send-reminders')->everyMinute();
