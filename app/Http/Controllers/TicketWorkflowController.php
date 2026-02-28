<?php

namespace App\Http\Controllers;

use App\Models\TicketProgress;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TicketWorkflowController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly TicketWorkflowService $workflow,
    ) {}

    /**
     * Admin TU → Verifikasi (SENT → VERIFIED)
     */
    public function verify(TicketProgress $ticket)
    {
        $this->authorize('verify', $ticket);

        $this->workflow->verify($ticket, auth()->user());

        return back()->with('success', 'Tiket berhasil diverifikasi dan diteruskan ke Pimpinan BPKH.');
    }

    /**
     * Pimpinan BPKH → Setujui (VERIFIED → APPROVED)
     */
    public function approve(TicketProgress $ticket)
    {
        $this->authorize('approve', $ticket);

        $this->workflow->approve($ticket, auth()->user());

        return back()->with('success', 'Tiket disetujui dan diteruskan ke Pimpinan PPKH.');
    }

    /**
     * Pimpinan PPKH → Disposisi ke Seksi (APPROVED → ASSIGNED)
     */
    public function assign(Request $request, TicketProgress $ticket)
    {
        $this->authorize('assign', $ticket);

        $data = $request->validate([
            'petugas_id' => ['required', 'exists:users,id'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        $petugasSeksi = User::role('seksi')
            ->whereNotNull('seksi_id')
            ->findOrFail($data['petugas_id']);

        $this->workflow->assignToSeksi(
            $ticket,
            auth()->user(),
            $petugasSeksi,
            $data['notes'] ?? null
        );

        $seksiName = $petugasSeksi->seksi ? $petugasSeksi->seksi->name : 'Seksi';

        return back()->with('success', "Tiket berhasil ditugaskan ke {$petugasSeksi->name} ({$seksiName}).");
    }

    /**
     * Seksi → Data Siap (ASSIGNED/REVISION → READY)
     */
    public function markReady(TicketProgress $ticket)
    {
        $this->authorize('markReady', $ticket);

        $this->workflow->markReady($ticket, auth()->user());

        return back()->with('success', 'Data ditandai siap.');
    }

    /**
     * Pimpinan PPKH → Review data (READY → UNDER_REVIEW_PPKH)
     */
    public function reviewPpkh(TicketProgress $ticket)
    {
        $this->authorize('reviewPpkh', $ticket);

        $this->workflow->reviewByPpkh($ticket, auth()->user());

        return back()->with('success', 'Data sedang ditinjau.');
    }

    /**
     * Pimpinan PPKH → Teruskan ke BPKH (UNDER_REVIEW_PPKH → UNDER_REVIEW_BPKH)
     */
    public function forwardToBpkh(TicketProgress $ticket)
    {
        $this->authorize('forwardToBpkh', $ticket);

        $this->workflow->forwardToBpkh($ticket, auth()->user());

        return back()->with('success', 'Data diteruskan ke Pimpinan BPKH.');
    }

    /**
     * Pimpinan PPKH/BPKH → Minta Revisi (UNDER_REVIEW_PPKH/BPKH → REVISION)
     */
    public function requestRevision(Request $request, TicketProgress $ticket)
    {
        // Bisa dari PPKH atau BPKH
        $user = auth()->user();

        if ($user->hasRole('pimpinan_ppkh')) {
            $this->authorize('requestRevision', $ticket);
        } else {
            $this->authorize('requestRevisionBpkh', $ticket);
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $this->workflow->requestRevision($ticket, $user, $data['reason']);

        return back()->with('success', 'Permintaan revisi dikirim ke Seksi.');
    }

    /**
     * Pimpinan BPKH → Final Approve (UNDER_REVIEW_BPKH → FINAL_APPROVED)
     */
    public function finalApprove(TicketProgress $ticket)
    {
        $this->authorize('finalApprove', $ticket);

        $this->workflow->finalApprove($ticket, auth()->user());

        return back()->with('success', 'Tiket disetujui final.');
    }

    /**
     * Admin TU → Selesaikan (FINAL_APPROVED → COMPLETED)
     */
    public function finalize(TicketProgress $ticket)
    {
        $this->authorize('finalize', $ticket);

        $this->workflow->finalize($ticket, auth()->user());

        return back()->with('success', 'Permohonan selesai.');
    }

    /**
     * Tolak permohonan
     */
    public function reject(Request $request, TicketProgress $ticket)
    {
        $this->authorize('reject', $ticket);

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $this->workflow->reject($ticket, auth()->user(), $data['reason']);

        return back()->with('error', 'Permohonan ditolak.');
    }
}
