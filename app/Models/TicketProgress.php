<?php

namespace App\Models;

use App\Enums\TicketStatus;
use App\Models\Assignment;

use Illuminate\Database\Eloquent\Model;

class TicketProgress extends Model
{
    protected $table = 'ticket_progress';

    protected $casts = [
        'status' => TicketStatus::class,
    ];

    protected $fillable = [
        'ticket_details_id',
        'status',
        'is_read',
        'notes',
    ];

    public function ticketDetails()
    {
        return $this->belongsTo(TicketDetail::class, 'ticket_details_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
