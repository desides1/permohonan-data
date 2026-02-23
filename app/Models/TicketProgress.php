<?php

namespace App\Models;

use App\Enums\TicketStatus;
use App\Models\Assignment;
use App\Enums\TicketAssignment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


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
        'current_assignment',
    ];

    protected $casts = [
        'status'             => TicketStatus::class,
        'current_assignment' => TicketAssignment::class,
        'is_read'            => 'boolean',

    ];

    protected $appends = ['status_label', 'current_assignment_label'];

    public function getStatusLabelAttribute(): ?string
    {
        return $this->status?->label();
    }
    public function getCurrentAssignmentLabelAttribute(): ?string
    {
        return $this->current_assignment?->label();
    }


    public function ticketDetails(): BelongsTo
    {
        return $this->belongsTo(TicketDetail::class, 'ticket_details_id', 'id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function latestAssignment()
    {
        return $this->hasOne(Assignment::class)->latestOfMany();
    }


    public function canTransitionTo(TicketStatus $target): bool
    {
        return $this->status->canTransitionTo($target);
    }

    public function transitionTo(TicketStatus $target, ?string $notes = null): void
    {
        if (! $this->canTransitionTo($target)) {
            abort(422, "Transisi dari {$this->status->label()} ke {$target->label()} tidak diizinkan.");
        }

        $this->update([
            'status'             => $target,
            'notes'              => $notes,
            'current_assignment' => TicketAssignment::nextAssignmentForStatus($target),
        ]);
    }
}
