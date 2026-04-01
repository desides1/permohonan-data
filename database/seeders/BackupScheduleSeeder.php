<?php

namespace Database\Seeders;

use App\Models\BackupSchedule;
use Illuminate\Database\Seeder;

class BackupScheduleSeeder extends Seeder
{
    public function run(): void
    {
        BackupSchedule::firstOrCreate([], [
            'frequency'        => 'daily',
            'time'             => '02:00',
            'is_active'        => true,
            'include_files'    => true,
            'include_database' => true,
            'auto_cleanup'     => true,
            'retention_days'   => 30,
            'next_run_at'      => now()->setTime(2, 0)->addDay(),
        ]);
    }
}
