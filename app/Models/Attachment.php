<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachment';

    protected $fillable = ['ticket_details_id', 'file_name', 'file_path'];

    public function ticketDetail()
    {
        return $this->belongsTo(TicketDetail::class, 'ticket_details_id', 'id');
    }
}
