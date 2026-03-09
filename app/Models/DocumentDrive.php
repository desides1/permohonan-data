<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentDrive extends Model
{
    protected $table = 'document_drive';

    protected $fillable = [
        'ticket_detail_id',
        'drive_file_id',
        'drive_folder_id',
        'original_name',
        'mime_type',
        'file_size',
        'keterangan',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function ticketDetail(): BelongsTo
    {
        return $this->belongsTo(TicketDetail::class, 'ticket_detail_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
