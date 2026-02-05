<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'ticket_detail_id',
        'service_usability',
        'service_satisfaction',
        'illegal_fee_indication',
        'suggestions',
    ];

    protected $table = 'feedback';

    public function ticketDetail()
    {
        return $this->belongsTo(TicketDetail::class, 'ticket_detail_id', 'id');
    }
}
