<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Enums\TicketAssignment;
use App\Models\Assignment;
use App\Models\TicketProgress;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketWorkflowController extends Controller
{
    use AuthorizesRequests;

    public function verify(TicketProgress $ticket)
    {
        $this->authorize('verify', $ticket);

        $ticket->update([
            'status' => TicketStatus::VERIFIED,
        ]);

        $ticket->assignments()->create([
            'assignment' => TicketAssignment::PIMPINAN_BPKH,
            'notes' => 'Tiket diverifikasi dan diteruskan ke Pimpinan BPKH.',
        ]);

        return back()->with('success', 'Tiket diverifikasi.');
    }

    public function approve(TicketProgress $ticket, Assignment $assignment)
    {
        $this->authorize('approve', $ticket);

        $ticket->update([
            'status' => TicketStatus::APPROVED,
        ]);

        $ticket->assignments()->create([
            'assignment' => TicketAssignment::PIMPINAN_PPKH,
            'notes' => 'Tiket disetujui oleh Pimpinan BPKH.',
        ]);

        return back()->with('success', 'Tiket disetujui pimpinan.');
    }

    public function reject(Request $request, TicketProgress $ticket)
    {
        $this->authorize('reject', $ticket);

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $ticket->update([
            'status' => TicketStatus::REJECTED,
        ]);

        $ticket->notes()->create([
            'notes' => 'DITOLAK: ' . $request->reason,
        ]);

        return back()->with('error', 'Permohonan ditolak.');
    }

    /**
     * SATU pintu assignment Seksi
     */
    public function assignSeksi(TicketProgress $ticket)
    {
        $this->authorize('assignSeksi', $ticket);

        $ticket->update([
            'status' => TicketStatus::ASSIGNED,
        ]);

        $ticket->assignments()->create([
            'assignment' => TicketAssignment::SEKSI,
            'notes' => 'Tiket ditugaskan ke Seksi terkait.',
        ]);

        return back()->with('success', 'Tiket ditugaskan ke Seksi.');
    }

    public function markReady(TicketProgress $ticket)
    {
        $this->authorize('markReady', $ticket);

        $ticket->update([
            'status' => TicketStatus::READY,
        ]);

        $ticket->assignments()->create([
            'assignment' => TicketAssignment::ADMIN_TU,
            'notes' => 'Data permohonan telah siap dan dikembalikan ke Admin TU.',
        ]);

        return back()->with('success', 'Data ditandai siap.');
    }

    public function finalize(TicketProgress $ticket)
    {
        $this->authorize('finalize', $ticket);

        $ticket->update([
            'status' => TicketStatus::COMPLETED,
        ]);

        return back()->with('success', 'Permohonan selesai.');
    }
}
