<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronJobLog extends Model
{
    protected $table = 'cron_job_logs';

    protected $fillable = [
        'command', 'status', 'processed_count', 'sent_count',
        'failed_count', 'skipped_count', 'notes', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
