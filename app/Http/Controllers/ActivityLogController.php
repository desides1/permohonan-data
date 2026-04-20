<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\TicketDetail;
use App\Models\TicketProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * (1) Aktivitas Permohonan Data
     */
    public function requestDataLog(Request $request)
    {
        $activities = Activity::query()
            ->where('log_name', 'ticket-workflow')
            ->with('causer:id,name')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('description', 'like', "%{$request->search}%")
                        ->orWhereJsonContains('properties->ticket_code', $request->search);
                });
            })
            ->when($request->action, fn($q) => $q->where('properties->action', $request->action))
            ->latest()
            ->paginate(15)
            ->through(fn(Activity $act) => [
                'id'          => $act->id,
                'ticket_code' => $act->properties['ticket_code'] ?? '-',
                'action'      => $act->properties['action'] ?? null,
                'description' => $act->description,
                'from_status' => $act->properties['from_status'] ?? null,
                'to_status'   => $act->properties['to_status'] ?? null,
                'causer'      => $act->causer?->name ?? 'Sistem',
                'properties'  => $act->properties->toArray(),
                'created_at'  => $act->created_at->format('d M Y H:i'),
                'time_ago'    => $act->created_at->diffForHumans(),
            ]);

        return Inertia::render('Admin/CatatanAktivitas/RequestDataLog', [
            'activities' => $activities,
            'filters'    => $request->only('search', 'action'),
        ]);
    }

    /**
     * Detail aktivitas per tiket
     */
    public function requestDataLogDetail(TicketDetail $ticket)
    {
        $ticket->load([
            'ticketProgress.assignments.assignedByUser',
            'ticketProgress.assignments.assignedToUser',
            'ticketProgress.assignments.seksi',
        ]);

        $activities = Activity::query()
            ->where('log_name', 'ticket-workflow')
            ->where('subject_type', TicketProgress::class)
            ->where('subject_id', $ticket->ticketProgress?->id)
            ->with('causer:id,name')
            ->latest()
            ->get()
            ->map(fn(Activity $act) => [
                'id'          => $act->id,
                'action'      => $act->properties['action'] ?? null,
                'description' => $act->description,
                'from_status' => $act->properties['from_status'] ?? null,
                'to_status'   => $act->properties['to_status'] ?? null,
                'reason'      => $act->properties['reason'] ?? null,
                'causer'      => $act->causer?->name ?? 'Sistem',
                'properties'  => $act->properties->toArray(),
                'created_at'  => $act->created_at->format('d M Y H:i'),
                'time_ago'    => $act->created_at->diffForHumans(),
            ]);

        $notifications = NotificationLog::query()
            ->where('ticket_detail_id', $ticket->id)
            ->latest()
            ->get()
            ->map(fn(NotificationLog $n) => [
                'id'            => $n->id,
                'type'          => $n->type,
                'channel'       => $n->channel,
                'recipient'     => $n->channel === 'email' ? $n->recipient_email : $n->recipient_phone,
                'status'        => $n->sent_at ? 'sent' : ($n->failed_at ? 'failed' : 'pending'),
                'error_message' => $n->error_message,
                'sent_at'       => $n->sent_at?->format('d M Y H:i'),
                'failed_at'     => $n->failed_at?->format('d M Y H:i'),
                'created_at'    => $n->created_at->format('d M Y H:i'),
            ]);

        return Inertia::render('Admin/CatatanAktivitas/DetilOfRequestDataLog', [
            'ticket'        => [
                'ticket_code'  => $ticket->ticket_code,
                'name'         => $ticket->name,
                'email'        => $ticket->email,
                'status'       => $ticket->ticketProgress?->status?->label(),
                'status_color' => $ticket->ticketProgress?->status?->color(),
                'created_at'   => $ticket->created_at->format('d M Y H:i'),
            ],
            'activities'    => $activities,
            'notifications' => $notifications,
        ]);
    }

    /**
     * (2) Aktivitas Notifikasi
     */
    public function notificationLog(Request $request)
    {
        $notifications = NotificationLog::query()
            ->with(['recipientUser:id,name', 'ticketDetail:id,ticket_code'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('recipient_email', 'like', "%{$request->search}%")
                        ->orWhere('recipient_phone', 'like', "%{$request->search}%")
                        ->orWhereHas('ticketDetail', fn($q2) => $q2->where('ticket_code', 'like', "%{$request->search}%"));
                });
            })
            ->when($request->channel, fn($q) => $q->where('channel', $request->channel))
            ->when($request->status, function ($q) use ($request) {
                match ($request->status) {
                    'sent'    => $q->sent(),
                    'failed'  => $q->failed(),
                    'pending' => $q->pending(),
                    default   => $q,
                };
            })
            ->latest()
            ->paginate(15)
            ->through(fn(NotificationLog $n) => [
                'id'            => $n->id,
                'ticket_code'   => $n->ticketDetail?->ticket_code ?? ($n->payload['ticket_code'] ?? '-'),
                'type'          => $n->type,
                'channel'       => $n->channel,
                'recipient'     => $n->channel === 'email' ? $n->recipient_email : $n->recipient_phone,
                'status'        => $n->sent_at ? 'sent' : ($n->failed_at ? 'failed' : 'pending'),
                'error_message' => $n->error_message,
                'payload'       => $n->payload,
                'sent_at'       => $n->sent_at?->format('d M Y H:i'),
                'failed_at'     => $n->failed_at?->format('d M Y H:i'),
                'created_at'    => $n->created_at->format('d M Y H:i'),
            ]);

        return Inertia::render('Admin/CatatanAktivitas/NotificationLog', [
            'notifications' => $notifications,
            'filters'       => $request->only('search', 'channel', 'status'),
        ]);
    }

    /**
     * (3) Aktivitas Pengguna
     */
    public function userLog(Request $request)
    {
        $activities = Activity::query()
            ->where('log_name', 'user-activity')
            ->with('causer:id,name,email')
            ->when($request->search, function ($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%")
                    ->orWhereHas('causer', fn($q2) => $q2->where('name', 'like', "%{$request->search}%"));
            })
            ->when($request->action, fn($q) => $q->where('properties->action', $request->action))
            ->latest()
            ->paginate(15)
            ->through(fn(Activity $act) => [
                'id'          => $act->id,
                'action'      => $act->properties['action'] ?? null,
                'description' => $act->description,
                'causer_name' => $act->causer?->name ?? 'Sistem',
                'causer_email' => $act->causer?->email ?? '-',
                'ip_address'  => $act->properties['ip_address'] ?? null,
                'user_agent'  => $act->properties['user_agent'] ?? null,
                'properties'  => $act->properties->toArray(),
                'created_at'  => $act->created_at->format('d M Y H:i'),
                'time_ago'    => $act->created_at->diffForHumans(),
            ]);

        return Inertia::render('Admin/CatatanAktivitas/UserLog', [
            'activities' => $activities,
            'filters'    => $request->only('search', 'action'),
        ]);
    }

    /**
     * Retry / kirim ulang notifikasi yang gagal
     */
    public function retryNotification(NotificationLog $notification)
    {
        $notification->update([
            'sent_at'       => null,
            'failed_at'     => null,
            'error_message' => null,
        ]);

        // Re-dispatch ke N8N
        app(\App\Services\N8nWebhookService::class)->dispatch($notification);

        return back()->with('success', "Notifikasi #{$notification->id} sedang dikirim ulang.");
    }
}
