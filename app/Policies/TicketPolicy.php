<?php

namespace App\Policies;

use App\Models\TicketProgress;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\TicketStatus;

class TicketPolicy
{
    use HandlesAuthorization;
    /**
     * Admin TU memverifikasi tiket
     */
    public function verify(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('admin_tu')
            && $ticket->status === TicketStatus::SENT;
    }

    /**
     * Pimpinan menyetujui tiket
     */
    public function approve(User $user, TicketProgress $ticket): bool
    {
        return $user->hasAnyRole(['pimpinan_bpkh', 'pimpinan_ppkh'])
            && $ticket->status === TicketStatus::VERIFIED;
    }

    /**
     * Penolakan (Admin TU atau Pimpinan)
     */
    public function reject(User $user, TicketProgress $ticket): bool
    {
        return $user->hasAnyRole(['admin_tu', 'pimpinan_bpkh', 'pimpinan_ppkh'])
            && ! in_array($ticket->status, [
                TicketStatus::COMPLETED,
                TicketStatus::REJECTED,
            ]);
    }

    /**
     * Admin TU assign ke Seksi 1
     */
    public function assign(User $user, TicketProgress $ticket): bool
    {
        return $user->hasAnyRole([
            'admin_tu',
            'pimpinan_bpkh',
            'pimpinan_ppkh',
            'seksi',
        ])
            && ! in_array($ticket->status, [
                TicketStatus::COMPLETED,
                TicketStatus::REJECTED,
            ]);
    }

    /**
     * Seksi 1 / 2 upload data
     */
    public function uploadData(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('seksi')
            && $ticket->status === TicketStatus::ASSIGNED;
    }

    /**
     * Seksi menandai data siap
     */
    public function markReady(User $user, TicketProgress $ticket): bool
    {
        return $this->uploadData($user, $ticket);
    }

    /**
     * Admin TU menyelesaikan tiket
     */
    public function finalize(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('admin_tu')
            && $ticket->status === TicketStatus::READY;
    }
}
