<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Enums\TicketStatus;
use App\Enums\TicketAssignment;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketWorkflowController extends Controller
{
    use AuthorizesRequests;

    public function verify(Ticket $ticket)
    {
        $this->authorize('verify', $ticket);

        $ticket->update([
            'status' => TicketStatus::VERIFIED,
            'assignment' => TicketAssignment::PIMPINAN_BPKH,
        ]);

        return back()->with('success', 'Tiket diverifikasi.');
    }

    public function approve(Ticket $ticket)
    {
        $this->authorize('approve', $ticket);

        $ticket->update([
            'status' => TicketStatus::APPROVED,
            'assignment' => TicketAssignment::PIMPINAN_PPKH,
        ]);

        return back()->with('success', 'Tiket disetujui pimpinan.');
    }

    public function reject(Request $request, Ticket $ticket)
    {
        $this->authorize('reject', $ticket);

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $ticket->update([
            'status' => TicketStatus::REJECTED,
            'assignment' => null,
        ]);

        $ticket->notes()->create([
            'notes' => 'DITOLAK: ' . $request->reason,
        ]);

        return back()->with('error', 'Permohonan ditolak.');
    }

    /**
     * SATU pintu assignment Seksi
     */
    public function assignSeksi(Ticket $ticket)
    {
        $this->authorize('assignSeksi', $ticket);

        $ticket->update([
            'status' => TicketStatus::ASSIGNED,
            'assignment' => TicketAssignment::SEKSI,
        ]);

        return back()->with('success', 'Tiket ditugaskan ke Seksi.');
    }

    public function markReady(Ticket $ticket)
    {
        $this->authorize('markReady', $ticket);

        $ticket->update([
            'status' => TicketStatus::READY,
            'assignment' => TicketAssignment::ADMIN_TU,
        ]);

        return back()->with('success', 'Data ditandai siap.');
    }

    public function finalize(Ticket $ticket)
    {
        $this->authorize('finalize', $ticket);

        $ticket->update([
            'status' => TicketStatus::COMPLETED,
            'assignment' => null,
        ]);

        return back()->with('success', 'Permohonan selesai.');
    }
}
