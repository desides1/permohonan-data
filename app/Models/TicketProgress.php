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
        'status',
        'is_read',
        'notes',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function ticketDetails()
    {
        return $this->hasMany(TicketDetail::class);
    }
}
