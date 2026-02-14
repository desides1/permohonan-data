<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'ticket_progress_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
    ];

    protected $table = 'assignments';

    protected $dates = [
        'assigned_at',
    ];

    public function ticketProgress()
    {
        return $this->belongsTo(TicketProgress::class, 'ticket_progress_id', 'id');
    }
}
