<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use App\Models\BackupSchedule;

Schedule::command('app:backup-to-drive  --type=scheduled')
    ->dailyAt('02:00')
    ->when(function () {
        $schedule = BackupSchedule::where('is_active', true)->first();
        return $schedule !== null;
    })
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/backup.log'))
    ->onFailure(function (Throwable $e) {
        // Log error jika backup gagal
        Log::error('Scheduled backup failed: ' . $e->getMessage());
    });

Schedule::command('backup:clean --disable-notifications')
    ->weeklyOn(7, '03:00')
    ->when(function () {
        $schedule = BackupSchedule::where('is_active', true)->first();
        return $schedule !== null && $schedule->auto_cleanup;
    })
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/backup-clean.log'))
    ->onFailure(function (Throwable $e) {
        Log::error('Scheduled backup cleanup failed: ' . $e->getMessage());
    });

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');
