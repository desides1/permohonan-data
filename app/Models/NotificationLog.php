<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $table = 'notification_logs';

    protected $fillable = [
        'type',
        'channel',
        'recipient_user_id',
        'recipient_email',
        'recipient_phone',
        'ticket_detail_id',
        'payload',
        'sent_at',
        'failed_at',
        'error_message',
    ];

    protected $casts = [
        'payload'   => 'array',
        'sent_at'   => 'datetime',
        'failed_at' => 'datetime',
    ];

    // ─── Scopes ──────────────────────────────────────
    public function scopePending($query)
    {
        return $query->whereNull('sent_at')->whereNull('failed_at');
    }

    public function scopeSent($query)
    {
        return $query->whereNotNull('sent_at');
    }

    public function scopeFailed($query)
    {
        return $query->whereNotNull('failed_at');
    }

    public function scopeForChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    // ─── Relations ───────────────────────────────────
    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function ticketDetail(): BelongsTo
    {
        return $this->belongsTo(TicketDetail::class, 'ticket_detail_id');
    }

    // ─── Helpers ─────────────────────────────────────
    public function markSent(): void
    {
        $this->update(['sent_at' => now()]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'failed_at'     => now(),
            'error_message' => $error,
        ]);
    }
}
