<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    protected $fillable = [
        'name',
        'email',
        'telp',
        'job',
        'postal_code',
        'institute',
        'address',
        'submit_data',
        'get_doc',
        'send_doc',
        'data_purpose',
        'details_data'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'ticket_details_id', 'id');
    }
}
