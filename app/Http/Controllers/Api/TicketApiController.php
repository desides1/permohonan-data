<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\TicketStatus;
use App\Models\TicketDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    /**
     * Tiket yang relevan untuk role user yang sedang login.
     * Digunakan untuk fitur notifikasi.
     */
    public function myTickets(Request $request): JsonResponse
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $visibleStatuses = TicketStatus::visibleForRole($role);

        $tickets = TicketDetail::query()
            ->with(['ticketProgress.latestAssignment'])
            ->whereHas('ticketProgress', function ($q) use ($visibleStatuses) {
                $q->whereIn('status', array_map(fn($s) => $s->value, $visibleStatuses));
            })
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'role'    => $role,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Tiket yang belum dibaca (untuk badge notifikasi).
     */
    public function unreadCount(): JsonResponse
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $visibleStatuses = TicketStatus::visibleForRole($role);

        $count = TicketDetail::query()
            ->whereHas('ticketProgress', function ($q) use ($visibleStatuses) {
                $q->whereIn('status', array_map(fn($s) => $s->value, $visibleStatuses))
                    ->where('is_read', false);
            })
            ->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Detail tiket tunggal.
     */
    public function show(TicketDetail $ticket): JsonResponse
    {
        $ticket->load([
            'ticketProgress.assignments.assignedByUser',
            'attachments',
        ]);

        return response()->json([
            'ticket' => $ticket,
        ]);
    }

    /**
     * Riwayat aktivitas tiket (dari Spatie Activity Log).
     */
    public function activityLog(TicketDetail $ticket): JsonResponse
    {
        $ticket->loadMissing('ticketProgress');

        $activities = \Spatie\Activitylog\Models\Activity::query()
            ->where('log_name', 'ticket-workflow')
            ->where('subject_type', \App\Models\TicketProgress::class)
            ->where('subject_id', $ticket->ticketProgress->id)
            ->latest()
            ->get()
            ->map(fn($activity) => [
                'id'          => $activity->id,
                'description' => $activity->description,
                'action'      => $activity->properties['action'] ?? null,
                'from_status' => $activity->properties['from_status'] ?? null,
                'to_status'   => $activity->properties['to_status'] ?? null,
                'causer'      => $activity->causer?->name,
                'created_at'  => $activity->created_at->toDateTimeString(),
            ]);

        return response()->json([
            'ticket_code' => $ticket->ticket_code,
            'activities'  => $activities,
        ]);
    }
}
