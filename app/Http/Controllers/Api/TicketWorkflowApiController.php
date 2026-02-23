<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TicketProgress;
use App\Services\TicketWorkflowService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketWorkflowApiController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly TicketWorkflowService $workflow,
    ) {}

    public function verify(TicketProgress $ticket): JsonResponse
    {
        $this->authorize('verify', $ticket);
        $this->workflow->verify($ticket, auth()->user());

        return response()->json([
            'message' => 'Tiket berhasil diverifikasi.',
            'status'  => $ticket->fresh()->status->value,
        ]);
    }

    public function approve(TicketProgress $ticket): JsonResponse
    {
        $this->authorize('approve', $ticket);
        $this->workflow->approve($ticket, auth()->user());

        return response()->json([
            'message' => 'Tiket disetujui.',
            'status'  => $ticket->fresh()->status->value,
        ]);
    }

    public function assign(Request $request, TicketProgress $ticket): JsonResponse
    {
        $this->authorize('assign', $ticket);

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->workflow->assignToSeksi($ticket, auth()->user(), $data['notes'] ?? null);

        return response()->json([
            'message' => 'Tiket ditugaskan ke Seksi.',
            'status'  => $ticket->fresh()->status->value,
        ]);
    }

    public function markReady(TicketProgress $ticket): JsonResponse
    {
        $this->authorize('markReady', $ticket);
        $this->workflow->markReady($ticket, auth()->user());

        return response()->json([
            'message' => 'Data ditandai siap.',
            'status'  => $ticket->fresh()->status->value,
        ]);
    }

    public function reviewPpkh(TicketProgress $ticket): JsonResponse
    {
        $this->authorize('reviewPpkh', $ticket);
        $this->workflow->reviewByPpkh($ticket, auth()->user());

        return response()->json([
            'message' => 'Data sedang ditinjau PPKH.',
            'status'  => $ticket->fresh()->status->value,
        ]);
    }

    public function forwardToBpkh(TicketProgress $ticket): JsonResponse
    {
        $this->authorize('forwardToBpkh', $ticket);
        $this->workflow->forwardToBpkh($ticket, auth()->user());

        return response()->json([
            'message' => 'Data diteruskan ke BPKH.',
            'status'  => $ticket->fresh()->status->value,
        ]);
    }

    public function requestRevision(Request $request, TicketProgress $ticket): JsonResponse
    {
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

        return response()->json([
            'message' => 'Revisi diminta.',
            'status'  => $ticket->fresh()->status->value,
        ]);
    }

    public function finalApprove(TicketProgress $ticket): JsonResponse
    {
        $this->authorize('finalApprove', $ticket);
        $this->workflow->finalApprove($ticket, auth()->user());

        return response()->json([
            'message' => 'Tiket disetujui final.',
            'status'  => $ticket->fresh()->status->value,
        ]);
    }

    public function finalize(TicketProgress $ticket): JsonResponse
    {
        $this->authorize('finalize', $ticket);
        $this->workflow->finalize($ticket, auth()->user());

        return response()->json([
            'message' => 'Permohonan selesai.',
            'status'  => $ticket->fresh()->status->value,
        ]);
    }

    public function reject(Request $request, TicketProgress $ticket): JsonResponse
    {
        $this->authorize('reject', $ticket);

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $this->workflow->reject($ticket, auth()->user(), $data['reason']);

        return response()->json([
            'message' => 'Permohonan ditolak.',
            'status'  => $ticket->fresh()->status->value,
        ]);
    }
}
