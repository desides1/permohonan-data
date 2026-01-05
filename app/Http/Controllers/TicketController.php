<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TicketController extends Controller
{

    public function show(Ticket $ticket)
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
                'assignSeksi1'  => Gate::allows('assignSeksi1', $ticket),
                'assignSeksi2'  => Gate::allows('assignSeksi2', $ticket),
                'markReady'     => Gate::allows('markReady', $ticket),
                'finalize'      => Gate::allows('finalize', $ticket),
            ],
        ]);
    }
}
