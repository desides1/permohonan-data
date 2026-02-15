<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\SubmitMethod;
use App\Enums\DocumentAccessType;
use App\Enums\DeliveryMethod;
use App\Models\TicketProgress;
use App\Models\Attachment;
use App\Models\Feedback;

class TicketDetail extends Model
{
    protected $fillable = [
        'ticket_code',
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
        'details_data',
    ];

    protected $table = 'ticket_detail';

    protected $casts = [
        'submit_data' => SubmitMethod::class,
        'get_doc' => DocumentAccessType::class,
        'send_doc' => DeliveryMethod::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'ticket_code';
    }

    public function ticketProgress()
    {
        return $this->hasOne(TicketProgress::class, 'ticket_details_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'ticket_details_id', 'id');
    }

    public function feedbacks()
    {
        return $this->hasOne(Feedback::class);
    }

    public function ticketReplies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_detail_id', 'id');
    }
}
