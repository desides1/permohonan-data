<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketWorkflowController extends Controller
{
    use AuthorizesRequests;
    public function verify(Ticket $ticket)
    {
        $this->authorize('verify', $ticket);

        $ticket->update([
            'status' => 'Diverifikasi oleh admin TU',
            'assignment' => 'Pimpinan',
        ]);

        return back()->with('success', 'Tiket diverifikasi.');
    }

    public function approve(Ticket $ticket)
    {
        $this->authorize('approve', $ticket);

        $ticket->update([
            'status' => 'Di setujui oleh pimpinan',
            'assignment' => 'Admin TU',
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
            'status' => 'Di tolak',
            'assignment' => null,
        ]);

        $ticket->notes()->create([
            'notes' => 'DITOLAK: ' . $request->reason,
        ]);

        return back()->with('error', 'Permohonan ditolak.');
    }

    public function assignSeksi1(Ticket $ticket)
    {
        $this->authorize('assignSeksi1', $ticket);

        $ticket->update([
            'status' => 'Ditugaskan ke penyedia 1',
            'assignment' => 'Seksi 1',
        ]);

        return back()->with('success', 'Ditugaskan ke Seksi 1.');
    }

    public function assignSeksi2(Ticket $ticket)
    {
        $this->authorize('assignSeksi2', $ticket);

        $ticket->update([
            'status' => 'Ditugaskan ke penyedia 2',
            'assignment' => 'Seksi 2',
        ]);

        return back()->with('success', 'Ditugaskan ke Seksi 2.');
    }

    public function markReady(Ticket $ticket)
    {
        $this->authorize('markReady', $ticket);

        $ticket->update([
            'status' => 'Data siap',
            'assignment' => 'Admin TU',
        ]);

        return back()->with('success', 'Data ditandai siap.');
    }

    public function finalize(Ticket $ticket)
    {
        $this->authorize('finalize', $ticket);

        $ticket->update([
            'status' => 'Selesai',
            'assignment' => null,
        ]);

        return back()->with('success', 'Permohonan selesai.');
    }
}
