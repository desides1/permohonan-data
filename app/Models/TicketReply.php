<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TicketDetail;
use App\Models\User;

class TicketReply extends Model
{

    protected $table = 'ticket_reply';

    protected $fillable = [
        'replied_by',
        'ticket_detail_id',
        'content',
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
