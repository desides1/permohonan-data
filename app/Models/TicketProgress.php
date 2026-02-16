<?php

namespace App\Models;

use App\Enums\TicketStatus;
use App\Models\Assignment;

use Illuminate\Database\Eloquent\Model;

class TicketProgress extends Model
{
    protected $table = 'ticket_progress';

    protected $primaryKey = 'id';

    protected $fillable = [
        'ticket_details_id',
        'status',
        'is_read',
        'notes',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
    ];

    protected $appends = ['status_label'];

    public function getStatusLabelAttribute(): ?string
    {
        return $this->status?->label();
    }

    public function ticketDetails()
    {
        return $this->belongsTo(TicketDetail::class, 'ticket_details_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
