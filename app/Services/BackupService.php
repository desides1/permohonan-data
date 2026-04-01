<?php

namespace App\Services;

use App\Models\BackupHistory;
use App\Models\BackupSchedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BackupService
{
    /* ------------------------------------------------------------------ */
    /*  Run Backup                                                        */
    /* ------------------------------------------------------------------ */

    public function runBackup(string $type = 'manual', ?int $userId = null): BackupHistory
    {
        $history = BackupHistory::create([
            'filename'   => 'backup-bpkh-' . now()->format('Y-m-d-H-i-s') . '.zip',
            'disk'       => 'google_backup',
            'type'       => $type,
            'status'     => 'in_progress',
            'created_by' => $userId,
        ]);

        try {
            // Hapus backup duplikat sebelum membuat yang baru (hari yang sama)
            $this->removeDuplicateBackupsForToday();

            // Jalankan spatie backup
            $exitCode = Artisan::call('backup:run', [
                '--disable-notifications' => true,
            ]);

            if ($exitCode !== 0) {
                throw new \RuntimeException('Backup command gagal dengan exit code: ' . $exitCode);
            }

            // Ambil info file backup terbaru dari disk
            $backupInfo = $this->getLatestBackupInfo();

            $history->update([
                'status'        => 'completed',
                'filename'      => $backupInfo['filename'] ?? $history->filename,
                'path'          => $backupInfo['path'] ?? null,
                'size_in_bytes' => $backupInfo['size'] ?? 0,
                'completed_at'  => now(),
            ]);

            Log::info('Backup berhasil', [
                'type'     => $type,
                'filename' => $history->filename,
                'user_id'  => $userId,
            ]);
        } catch (Throwable $e) {
            $history->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Backup gagal', [
                'type'    => $type,
                'error'   => $e->getMessage(),
                'user_id' => $userId,
            ]);

            throw $e;
        }

        return $history->refresh();
    }

    /* ------------------------------------------------------------------ */
    /*  Scheduled Backup                                                  */
    /* ------------------------------------------------------------------ */

    public function runScheduledBackup(): ?BackupHistory
    {
        $schedule = BackupSchedule::where('is_active', true)->first();

        if (!$schedule) {
            Log::info('Tidak ada jadwal backup yang aktif.');
            return null;
        }

        $history = $this->runBackup('scheduled');

        $schedule->update([
            'last_run_at' => now(),
            'next_run_at' => $this->calculateNextRun($schedule),
        ]);

        // Jalankan cleanup otomatis jika diaktifkan
        if ($schedule->auto_cleanup) {
            $this->runCleanup();
        }

        return $history;
    }

    /* ------------------------------------------------------------------ */
    /*  Cleanup                                                           */
    /* ------------------------------------------------------------------ */

    public function runCleanup(): void
    {
        try {
            Artisan::call('backup:clean', [
                '--disable-notifications' => true,
            ]);

            Log::info('Cleanup backup berhasil.');
        } catch (Throwable $e) {
            Log::error('Cleanup backup gagal', ['error' => $e->getMessage()]);
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Duplicate Prevention (Auto Sync)                                  */
    /* ------------------------------------------------------------------ */

    public function removeDuplicateBackupsForToday(): void
    {
        try {
            $disk = Storage::disk('google_backup');
            $files = $disk->allFiles(config('app.name', 'BPKH') . '-Backup');
            $todayPrefix = 'backup-bpkh-' . now()->format('Y-m-d');

            $todayFiles = collect($files)
                ->filter(fn(string $file) => str_contains(basename($file), $todayPrefix))
                ->sortByDesc(fn(string $file) => $disk->lastModified($file));

            // Simpan file terbaru, hapus yang lama (deduplicate)
            $todayFiles->skip(1)->each(function (string $file) use ($disk) {
                $disk->delete($file);
                Log::info('Backup duplikat dihapus', ['file' => $file]);
            });
        } catch (Throwable $e) {
            Log::warning('Gagal membersihkan backup duplikat', ['error' => $e->getMessage()]);
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Schedule Management                                               */
    /* ------------------------------------------------------------------ */

    public function updateSchedule(array $data, int $userId): BackupSchedule
    {
        $schedule = BackupSchedule::firstOrNew();

        $schedule->fill(array_merge($data, [
            'updated_by' => $userId,
            'next_run_at' => $this->calculateNextRun(
                $schedule->fill($data)
            ),
        ]));

        $schedule->save();

        return $schedule->refresh();
    }

    public function getSchedule(): ?BackupSchedule
    {
        return BackupSchedule::first();
    }

    /* ------------------------------------------------------------------ */
    /*  Statistics                                                        */
    /* ------------------------------------------------------------------ */

    public function getStatistics(): array
    {
        $histories = BackupHistory::query();

        return [
            'total_backups'      => (clone $histories)->completed()->count(),
            'total_size'         => $this->formatBytes((clone $histories)->completed()->sum('size_in_bytes')),
            'last_backup'        => BackupHistory::completed()->latest()->first(),
            'failed_count'       => (clone $histories)->where('status', 'failed')->count(),
            'today_backup_count' => (clone $histories)->completed()->whereDate('created_at', today())->count(),
            'schedule'           => $this->getSchedule(),
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                           */
    /* ------------------------------------------------------------------ */

    private function getLatestBackupInfo(): array
    {
        try {
            $disk = Storage::disk('google_backup');
            $backupName = config('app.name', 'BPKH') . '-Backup';
            $files = $disk->allFiles($backupName);

            if (empty($files)) {
                return [];
            }

            $latestFile = collect($files)
                ->sortByDesc(fn(string $file) => $disk->lastModified($file))
                ->first();

            return [
                'filename' => basename($latestFile),
                'path'     => $latestFile,
                'size'     => $disk->size($latestFile),
            ];
        } catch (Throwable $e) {
            Log::warning('Gagal mendapatkan info backup terbaru', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function calculateNextRun(BackupSchedule $schedule): \Carbon\Carbon
    {
        $time = \Carbon\Carbon::parse($schedule->time);

        return match ($schedule->frequency) {
            'daily'   => now()->setTimeFrom($time)->addDay(),
            'weekly'  => now()->setTimeFrom($time)->addWeek(),
            'monthly' => now()->setTimeFrom($time)->addMonth(),
            default   => now()->setTimeFrom($time)->addDay(),
        };
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
