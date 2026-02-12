<?php

namespace App\Http\Controllers;

use App\Models\TicketDetail;
use App\Models\TicketProgress;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TicketController extends Controller
{

    public function show(TicketProgress $ticket)
    {
        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket->load([
                'detail',
                'attachments',
                'notes',
                'replies.user',
            ]),
            'can' => [
                'verify'        => Gate::allows('verify', $ticket),
                'approve'       => Gate::allows('approve', $ticket),
                'reject'        => Gate::allows('reject', $ticket),
                'assign'        => Gate::allows('assign', $ticket),
                'markReady'     => Gate::allows('markReady', $ticket),
                'finalize'      => Gate::allows('finalize', $ticket),
            ],
        ]);
    }

    public function showBeranda()
    {
        $tickets = TicketDetail::query()
            ->with(['ticketProgress'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($ticket) => [
                'ticket_code' => $ticket->ticket_code,
                'name' => $ticket->name,
                'status' => $ticket->ticketProgress?->status?->label(),
            ]);

        return Inertia::render('Admin/Beranda/Beranda', [
            'tickets' => $tickets,
            'distribution' => [
                'Baru' => 30,
                'Proses' => 45,
                'Selesai' => 20,
                'Tolak' => 5,
            ],
        ]);
    }
}
