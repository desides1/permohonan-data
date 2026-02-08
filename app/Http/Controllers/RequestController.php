<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitRequest;
use App\Models\TicketDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Ticket;
use App\Models\Attachment;
use App\Models\TicketProgress;
use App\Services\RequestSubmissionService;
use Illuminate\Validation\ValidationException;

class RequestController extends Controller
{
    public function showRequest()
    {
        return Inertia::render('LandingPage/Form');
    }

    public function postRequest(
        SubmitRequest $request,
        RequestSubmissionService $service
    ) {
        $detail = $service->submit($request->validated());

        return redirect()->back()->with(
            [
                'success'     => 'Permohonan berhasil dikirim. Silakan simpan kode tiket Anda.',
                'ticket_code' => $detail->ticket_code,
            ]
        );
    }

    public function trackTicket(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
        ]);

        $detail = TicketDetail::where('ticket_code', $request->ticket_code)
            ->with('ticketProgress')
            ->first();

        if (! $detail) {
            throw ValidationException::withMessages([
                'ticket_code' => 'Kode tiket tidak ditemukan. Silakan periksa kembali.',
            ]);
        }

        $progress = $detail->ticketProgress;

        $ticket = [
            'updated_at' => $progress?->updated_at ?? null,
            'ticket_code' => $detail->ticket_code,
            'status'      => $progress?->status ?? null,
            'notes'       => $progress?->notes ?? null,
        ];

        return Inertia::render('LandingPage/TrackRequest', [
            'ticket' => $ticket,
        ]);
    }
}
