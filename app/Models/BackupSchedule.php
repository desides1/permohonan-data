<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'frequency',
        'time',
        'is_active',
        'include_files',
        'include_database',
        'auto_cleanup',
        'retention_days',
        'last_run_at',
        'next_run_at',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active'        => 'boolean',
            'include_files'    => 'boolean',
            'include_database' => 'boolean',
            'auto_cleanup'     => 'boolean',
            'retention_days'   => 'integer',
            'last_run_at'      => 'datetime',
            'next_run_at'      => 'datetime',
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'daily'   => 'Harian',
            'weekly'  => 'Mingguan',
            'monthly' => 'Bulanan',
            default   => '-',
        };
    }
}
