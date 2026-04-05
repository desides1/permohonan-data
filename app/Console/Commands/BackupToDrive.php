<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;
use Throwable;

class BackupToDrive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-to-drive
                            {--type=scheduled : Tipe backup (manual/scheduled)}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan pencadangan data ke Google Drive';

    public function __construct(
        private readonly BackupService $backupService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->info('Memulai proses pencadangan...');

        try {
            $type = $this->option('type');
            $history = $type === 'scheduled'
                ? $this->backupService->runScheduledBackup()
                : $this->backupService->runBackup($type);

            if (!$history) {
                $this->warn('Tidak ada backup yang dijalankan karena jadwal tidak aktif atau kondisi tidak terpenuhi.');
                return self::SUCCESS;
            }

            $this->info('Backup berhasil dengan nama file: ' . $history->filename);
            $this->info('Ukuran file: ' . $history->formatted_size);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error("Pencadangan Gagal: {$e->getMessage()}");

            return self::FAILURE;
        }

        // $filename = 'backup_' . now()->format('Y-m-d_H-i') . '.sql';

        // $path = storage_path('app/' . $filename);

        // exec("mysqldump -u" . env('DB_USERNAME') . " -p" . env('DB_PASSWORD') . " " . env('DB_DATABASE') . " > {$path}");

        // app(\App\Services\GoogleDriveService::class)
        //     ->uploadFile(new \Illuminate\Http\File($path), $filename, env('GOOGLE_DRIVE_ROOT_FOLDER_ID'));

        // unlink($path);

        // $this->info('Backup berhasil.');
    }
}
