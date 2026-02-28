<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentNotificationApiController extends Controller
{
    /**
     * Penugasan yang ditujukan ke user yang sedang login.
     * Untuk notifikasi: "Anda mendapat tugas baru".
     */
    public function myAssignments(Request $request): JsonResponse
    {
        $user = auth()->user();

        $assignments = Assignment::query()
            ->where('assigned_to_user_id', $user->id)
            ->with([
                'ticketProgress.ticketDetails',
                'assignedByUser:id,name',
                'seksi:id,nama',
            ])
            ->latest('assigned_at')
            ->paginate($request->input('per_page', 15));

        $mapped = $assignments->through(fn(Assignment $a) => [
            'id'           => $a->id,
            'ticket_code'  => $a->ticketProgress?->ticketDetails?->ticket_code,
            'ticket_id'    => $a->ticketProgress?->ticketDetails?->id,
            'status'       => $a->ticketProgress?->status?->label(),
            'status_value' => $a->ticketProgress?->status?->value,
            'assignment'   => $a->assignment?->label(),
            'assigned_by'  => $a->assignedByUser?->name,
            'seksi'        => $a->seksi?->nama,
            'notes'        => $a->notes,
            'assigned_at'  => $a->assigned_at?->format('d M Y H:i'),
            'is_read'      => $a->is_read ?? false,
        ]);

        return response()->json($mapped);
    }

    /**
     * Jumlah penugasan belum dibaca — untuk badge notifikasi.
     */
    public function unreadAssignmentCount(): JsonResponse
    {
        $user = auth()->user();

        $count = Assignment::query()
            ->where('assigned_to_user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Tandai penugasan sebagai sudah dibaca.
     */
    public function markAsRead(Assignment $assignment): JsonResponse
    {
        if ($assignment->assigned_to_user_id !== auth()->id()) {
            abort(403);
        }

        $assignment->update(['is_read' => true]);

        return response()->json([
            'message' => 'Penugasan ditandai sudah dibaca.',
        ]);
    }

    /**
     * Tandai semua penugasan sebagai sudah dibaca.
     */
    public function markAllAsRead(): JsonResponse
    {
        Assignment::query()
            ->where('assigned_to_user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'Semua penugasan ditandai sudah dibaca.',
        ]);
    }

    /**
     * Penugasan berdasarkan role (untuk semua petugas di role tertentu).
     * Berguna untuk pimpinan melihat siapa yang sudah ditugaskan.
     */
    public function assignmentsByRole(Request $request): JsonResponse
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        // Hanya pimpinan yang boleh lihat assignment semua petugas
        if (!in_array($role, ['pimpinan_ppkh', 'pimpinan_bpkh', 'admin_tu'])) {
            abort(403);
        }

        $assignments = Assignment::query()
            ->whereNotNull('assigned_to_user_id')
            ->with([
                'ticketProgress.ticketDetails',
                'assignedByUser:id,name',
                'assignedToUser:id,name,seksi_id',
                'assignedToUser.seksi:id,nama',
                'seksi:id,nama',
            ])
            ->latest('assigned_at')
            ->paginate($request->input('per_page', 20));

        $mapped = $assignments->through(fn(Assignment $a) => [
            'id'            => $a->id,
            'ticket_code'   => $a->ticketProgress?->ticketDetails?->ticket_code,
            'assigned_to'   => $a->assignedToUser?->name,
            'assigned_by'   => $a->assignedByUser?->name,
            'seksi'         => $a->seksi?->nama,
            'notes'         => $a->notes,
            'status'        => $a->ticketProgress?->status?->label(),
            'assigned_at'   => $a->assigned_at?->format('d M Y H:i'),
        ]);

        return response()->json($mapped);
    }
}
