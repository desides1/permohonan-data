<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackupScheduleRequest;
use App\Models\BackupHistory;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class BackupController extends Controller
{
    public function __construct(
        private readonly BackupService $backupService,
    ) {}

    /* ------------------------------------------------------------------ */
    /*  Halaman Utama Pencadangan                                         */
    /* ------------------------------------------------------------------ */

    public function index(): Response
    {
        $statistics = $this->backupService->getStatistics();

        $histories = BackupHistory::with('creator')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->through(fn(BackupHistory $h) => [
                'id'             => $h->id,
                'filename'       => $h->filename,
                'formatted_size' => $h->formatted_size,
                'type'           => $h->type,
                'type_label'     => $h->type_label,
                'status'         => $h->status,
                'status_label'   => $h->status_label,
                'error_message'  => $h->error_message,
                'creator'        => $h->creator?->name ?? 'Sistem',
                'created_at'     => $h->created_at->format('d M Y, H:i'),
                'completed_at'   => $h->completed_at?->format('d M Y, H:i'),
            ]);

        $schedule = $this->backupService->getSchedule();

        return Inertia::render('Admin/Pencadangan/BackUpData', [
            'statistics' => [
                'total_backups'      => $statistics['total_backups'],
                'total_size'         => $statistics['total_size'],
                'failed_count'       => $statistics['failed_count'],
                'today_backup_count' => $statistics['today_backup_count'],
                'last_backup'        => $statistics['last_backup'] ? [
                    'filename'     => $statistics['last_backup']->filename,
                    'created_at'   => $statistics['last_backup']->created_at->format('d M Y, H:i'),
                    'status_label' => $statistics['last_backup']->status_label,
                ] : null,
            ],
            'histories' => $histories,
            'schedule'  => $schedule ? [
                'id'               => $schedule->id,
                'frequency'        => $schedule->frequency,
                'frequency_label'  => $schedule->frequency_label,
                'time'             => $schedule->time,
                'is_active'        => $schedule->is_active,
                'include_files'    => $schedule->include_files,
                'include_database' => $schedule->include_database,
                'auto_cleanup'     => $schedule->auto_cleanup,
                'retention_days'   => $schedule->retention_days,
                'last_run_at'      => $schedule->last_run_at?->format('d M Y, H:i'),
                'next_run_at'      => $schedule->next_run_at?->format('d M Y, H:i'),
            ] : null,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  Backup Manual                                                     */
    /* ------------------------------------------------------------------ */

    public function store(): RedirectResponse
    {
        try {
            $this->backupService->runBackup('manual', auth()->id());

            return redirect()
                ->route('admin.backup.index')
                ->with('success', 'Pencadangan data berhasil dilakukan.');
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.backup.index')
                ->with('error', 'Pencadangan data gagal: ' . $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Update Jadwal                                                     */
    /* ------------------------------------------------------------------ */

    public function updateSchedule(BackupScheduleRequest $request): RedirectResponse
    {
        $this->backupService->updateSchedule(
            $request->validated(),
            auth()->id(),
        );

        return redirect()
            ->route('admin.backup.index')
            ->with('success', 'Jadwal pencadangan berhasil diperbarui.');
    }

    /* ------------------------------------------------------------------ */
    /*  Cleanup Manual                                                    */
    /* ------------------------------------------------------------------ */

    public function cleanup(): RedirectResponse
    {
        try {
            $this->backupService->runCleanup();

            return redirect()
                ->route('admin.backup.index')
                ->with('success', 'Pembersihan data cadangan lama berhasil.');
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.backup.index')
                ->with('error', 'Pembersihan gagal: ' . $e->getMessage());
        }
    }
}
