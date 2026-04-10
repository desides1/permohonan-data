<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitRequest;
use App\Models\Feedback;
use App\Models\TicketDetail;
use App\Services\PemohonAutoRegisterService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\RequestSubmissionService;
use Illuminate\Validation\ValidationException;
use App\Enums\TicketStatus;

class RequestController extends Controller
{
    public function __construct(
        private PemohonAutoRegisterService $autoRegister
    ) {}

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
        $statusEnum = $progress?->status;
        $ticket = [
            'updated_at' => $progress?->updated_at->format('d M Y') ?? null,
            'ticket_code' => $detail->ticket_code,
            'status'       => $statusEnum?->label() ?? $progress?->status ?? '-',
            'status_value' => $statusEnum?->value ?? $progress?->status,
            'status_color' => $statusEnum?->color() ?? '#6b7280',
            'status_icon'  => $statusEnum?->icon(),
            'notes'       => $progress?->notes ?? null,
        ];

        return Inertia::render('LandingPage/TrackRequest', [
            'ticket' => $ticket,
        ]);
    }

    public function showFeedbackForm(Request $request)
    {
        return Inertia::render('LandingPage/FeedbackForm', [
            'ticket_code' => $request->string('ticket_code')->toString(),
        ]);
    }

    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'ticket_code'             => 'required|string',
            'service_usability'       => 'required|string|max:100',
            'service_satisfaction'    => 'required|string|max:100',
            'illegal_fee_indication'  => 'required|string|max:100',
            'suggestions'             => 'nullable|string|max:1000',
        ]);

        $detail = TicketDetail::where('ticket_code', $validated['ticket_code'])->first();

        if (! $detail) {
            throw ValidationException::withMessages([
                'ticket_code' => 'Kode tiket tidak ditemukan. Silakan periksa kembali.',
            ]);
        }

        Feedback::create([
            'ticket_detail_id'       => $detail->id,
            'service_usability'      => $validated['service_usability'],
            'service_satisfaction'   => $validated['service_satisfaction'],
            'illegal_fee_indication' => $validated['illegal_fee_indication'],
            'suggestions'            => $validated['suggestions'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Terima kasih. Feedback Anda berhasil dikirim.');
    }
}
