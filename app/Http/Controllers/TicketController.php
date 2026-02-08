<?php

namespace App\Http\Controllers;

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
}
