<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupToDrive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-to-drive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = 'backup_' . now()->format('Y-m-d_H-i') . '.sql';

        $path = storage_path('app/' . $filename);

        exec("mysqldump -u" . env('DB_USERNAME') . " -p" . env('DB_PASSWORD') . " " . env('DB_DATABASE') . " > {$path}");

        app(\App\Services\GoogleDriveService::class)
            ->uploadFile(new \Illuminate\Http\File($path), $filename, env('GOOGLE_DRIVE_ROOT_FOLDER_ID'));

        unlink($path);

        $this->info('Backup berhasil.');
    }
}
