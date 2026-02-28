<?php

namespace App\Models;

use App\Enums\TicketAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Assignment extends Model
{
    protected $table = 'assignments';

    protected $fillable = [
        'ticket_progress_id',
        'assigned_to_user_id',
        'seksi_id',
        'assignment',
        'assigned_by',
        'notes',
        'assigned_at',
    ];



    protected $casts = [
        'assignment' => TicketAssignment::class,
        'assigned_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->assigned_by ??= Auth::id();
            $model->assigned_at ??= now();
        });
    }

    public function ticketProgress()
    {
        return $this->belongsTo(TicketProgress::class, 'ticket_progress_id', 'id');
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function seksi(): BelongsTo
    {
        return $this->belongsTo(Seksi::class);
    }
}
