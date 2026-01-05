<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    /**
     * Admin TU memverifikasi tiket
     */
    public function verify(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin TU')
            && $ticket->status === 'Dikirim';
    }

    /**
     * Pimpinan menyetujui tiket
     */
    public function approve(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Pimpinan')
            && $ticket->status === 'Diverifikasi oleh admin TU';
    }

    /**
     * Penolakan (Admin TU atau Pimpinan)
     */
    public function reject(User $user, Ticket $ticket): bool
    {
        return $user->hasAnyRole(['Admin TU', 'Pimpinan'])
            && $ticket->status !== 'Selesai'
            && $ticket->status !== 'Di tolak';
    }

    /**
     * Admin TU assign ke Seksi 1
     */
    public function assignSeksi1(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin TU')
            && $ticket->status === 'Di setujui oleh pimpinan';
    }

    /**
     * Admin TU assign ke Seksi 2
     */
    public function assignSeksi2(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin TU')
            && $ticket->status === 'Di setujui oleh pimpinan';
    }

    /**
     * Seksi 1 / 2 upload data
     */
    public function uploadData(User $user, Ticket $ticket): bool
    {
        return (
            $user->hasRole('Seksi 1')
            && $ticket->status === 'Ditugaskan ke penyedia 1'
        ) || (
            $user->hasRole('Seksi 2')
            && $ticket->status === 'Ditugaskan ke penyedia 2'
        );
    }

    /**
     * Seksi menandai data siap
     */
    public function markReady(User $user, Ticket $ticket): bool
    {
        return $this->uploadData($user, $ticket);
    }

    /**
     * Admin TU menyelesaikan tiket
     */
    public function finalize(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin TU')
            && $ticket->status === 'Data siap';
    }
}
