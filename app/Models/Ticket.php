<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_code',
        'status',
        'priority',
        'assignment',
    ];

    public function detail()
    {
        return $this->hasOne(TicketDetail::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
