<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['ticket_id', 'notes'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
