<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    /**
     * GET /api/v1/notifications/pending
     * N8N polls endpoint ini untuk mengambil notifikasi yang belum terkirim.
     */
    public function pending(Request $request): JsonResponse
    {
        $channel = $request->input('channel', 'email'); // email | whatsapp
        $limit   = $request->input('limit', 20);

        $notifications = NotificationLog::query()
            ->pending()
            ->forChannel($channel)
            ->with(['recipientUser:id,name,email', 'ticketDetail:id,ticket_code,name'])
            ->oldest()
            ->limit($limit)
            ->get()
            ->map(fn(NotificationLog $n) => [
                'id'              => $n->id,
                'type'            => $n->type,
                'channel'         => $n->channel,
                'recipient_email' => $n->recipient_email,
                'recipient_phone' => $n->recipient_phone,
                'recipient_name'  => $n->recipientUser?->name ?? $n->payload['name'] ?? null,
                'ticket_code'     => $n->ticketDetail?->ticket_code ?? $n->payload['ticket_code'] ?? null,
                'payload'         => $n->payload,
                'created_at'      => $n->created_at->toIso8601String(),
            ]);

        return response()->json([
            'data'  => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    /**
     * PATCH /api/v1/notifications/{id}/mark-sent
     * N8N memanggil ini setelah berhasil mengirim notifikasi.
     */
    public function markSent(NotificationLog $notification): JsonResponse
    {
        $notification->markSent();

        return response()->json([
            'message' => 'Notification marked as sent.',
            'sent_at' => $notification->fresh()->sent_at->toIso8601String(),
        ]);
    }

    /**
     * PATCH /api/v1/notifications/{id}/mark-failed
     * N8N memanggil ini jika gagal mengirim.
     */
    public function markFailed(Request $request, NotificationLog $notification): JsonResponse
    {
        $request->validate([
            'error_message' => 'required|string|max:1000',
        ]);

        $notification->markFailed($request->input('error_message'));

        return response()->json([
            'message' => 'Notification marked as failed.',
        ]);
    }

    /**
     * PATCH /api/v1/notifications/mark-sent-batch
     * N8N batch update setelah mengirim banyak notifikasi.
     */
    public function markSentBatch(Request $request): JsonResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:notification_logs,id',
        ]);

        NotificationLog::whereIn('id', $request->input('ids'))
            ->pending()
            ->update(['sent_at' => now()]);

        return response()->json([
            'message' => 'Notifications marked as sent.',
            'count'   => count($request->input('ids')),
        ]);
    }

    /**
     * GET /api/v1/notifications/stats
     * Dashboard statistik notifikasi.
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'pending_email'    => NotificationLog::pending()->forChannel('email')->count(),
            'pending_whatsapp' => NotificationLog::pending()->forChannel('whatsapp')->count(),
            'sent_today'       => NotificationLog::sent()->whereDate('sent_at', today())->count(),
            'failed_today'     => NotificationLog::failed()->whereDate('failed_at', today())->count(),
        ]);
    }
}
