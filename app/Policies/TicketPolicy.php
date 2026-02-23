<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\TicketProgress;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Admin TU memverifikasi tiket yang baru dikirim.
     * SENT → VERIFIED
     */
    public function verify(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('admin_tu')
            && $ticket->status === TicketStatus::SENT;
    }

    /**
     * Pimpinan BPKH menyetujui tiket yang sudah diverifikasi.
     * VERIFIED → APPROVED
     */
    public function approve(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('pimpinan_bpkh')
            && $ticket->status === TicketStatus::VERIFIED;
    }

    /**
     * Pimpinan PPKH disposisi ke Seksi.
     * APPROVED → ASSIGNED
     */
    public function assign(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('pimpinan_ppkh')
            && $ticket->status === TicketStatus::APPROVED;
    }

    /**
     * Seksi menandai data siap.
     * ASSIGNED → READY  |  REVISION → READY
     */
    public function markReady(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('seksi')
            && in_array($ticket->status, [
                TicketStatus::ASSIGNED,
                TicketStatus::REVISION,
            ]);
    }

    /**
     * Pimpinan PPKH me-review data dari Seksi.
     * READY → UNDER_REVIEW_PPKH
     */
    public function reviewPpkh(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('pimpinan_ppkh')
            && $ticket->status === TicketStatus::READY;
    }

    /**
     * Pimpinan PPKH meneruskan ke BPKH setelah review OK.
     * UNDER_REVIEW_PPKH → UNDER_REVIEW_BPKH
     */
    public function forwardToBpkh(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('pimpinan_ppkh')
            && $ticket->status === TicketStatus::UNDER_REVIEW_PPKH;
    }

    /**
     * Pimpinan PPKH minta revisi ke Seksi.
     * UNDER_REVIEW_PPKH → REVISION
     */
    public function requestRevision(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('pimpinan_ppkh')
            && $ticket->status === TicketStatus::UNDER_REVIEW_PPKH;
    }

    /**
     * Pimpinan BPKH final approve.
     * UNDER_REVIEW_BPKH → FINAL_APPROVED
     */
    public function finalApprove(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('pimpinan_bpkh')
            && $ticket->status === TicketStatus::UNDER_REVIEW_BPKH;
    }

    /**
     * Pimpinan BPKH minta revisi saat review BPKH.
     * UNDER_REVIEW_BPKH → REVISION
     */
    public function requestRevisionBpkh(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('pimpinan_bpkh')
            && $ticket->status === TicketStatus::UNDER_REVIEW_BPKH;
    }

    /**
     * Admin TU menyelesaikan tiket.
     * FINAL_APPROVED → COMPLETED
     */
    public function finalize(User $user, TicketProgress $ticket): bool
    {
        return $user->hasRole('admin_tu')
            && $ticket->status === TicketStatus::FINAL_APPROVED;
    }

    /**
     * Penolakan oleh role tertentu pada status tertentu.
     */
    public function reject(User $user, TicketProgress $ticket): bool
    {
        if ($ticket->status->isTerminal()) {
            return false;
        }

        // Admin TU bisa tolak saat SENT
        if ($user->hasRole('admin_tu') && $ticket->status === TicketStatus::SENT) {
            return true;
        }

        // Pimpinan BPKH bisa tolak saat VERIFIED atau UNDER_REVIEW_BPKH
        if ($user->hasRole('pimpinan_bpkh') && in_array($ticket->status, [
            TicketStatus::VERIFIED,
            TicketStatus::UNDER_REVIEW_BPKH,
        ])) {
            return true;
        }

        // Pimpinan PPKH bisa tolak saat APPROVED
        if ($user->hasRole('pimpinan_ppkh') && $ticket->status === TicketStatus::APPROVED) {
            return true;
        }

        return false;
    }
}
