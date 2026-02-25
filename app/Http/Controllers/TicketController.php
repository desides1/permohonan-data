<?php

namespace App\Http\Controllers;

use App\Models\TicketDetail;
use App\Models\Attachment;
use App\Enums\TicketStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function show(TicketDetail $ticket)
    {
        $ticket->load([
            'ticketProgress.assignments.assignedByUser',
            'attachments',
            'ticketReplies',
        ]);

        $is_read = $ticket->ticketProgress?->update(['is_read' => true]);

        if ($ticket->ticketProgress) {
            $ticket->ticketProgress->setAttribute(
                'status_color',
                $ticket->ticketProgress->status?->color(),
            );
        }

        $suratPermohonan = $ticket->attachments
            ->first(fn($a) => str_contains($a->file_path, 'surat_permohonan'));

        $lampiranLainnya = $ticket->attachments
            ->filter(fn($a) => ! str_contains($a->file_path, 'surat_permohonan'))
            ->values();

        // Ambil riwayat aktivitas dari Spatie
        $activities = \Spatie\Activitylog\Models\Activity::query()
            ->where('log_name', 'ticket-workflow')
            ->where('subject_type', \App\Models\TicketProgress::class)
            ->where('subject_id', $ticket->ticketProgress->id)
            ->latest()
            ->get()
            ->map(fn($act) => [
                'id'          => $act->id,
                'description' => $act->description,
                'action'      => $act->properties['action'] ?? null,
                'from_status' => $act->properties['from_status'] ?? null,
                'to_status'   => $act->properties['to_status'] ?? null,
                'causer'      => $act->causer?->name,
                'created_at'  => $act->created_at->format('d M Y H:i'),
            ]);

        return Inertia::render('Admin/DataPermohonan/Detail/Show', [
            'ticket'           => $ticket,
            'suratPermohonan'  => $suratPermohonan,
            'lampiranLainnya'  => $lampiranLainnya,
            'is_read'          => $is_read,
            'activities'       => $activities,
            'can' => [
                'verify'           => Gate::allows('verify', $ticket->ticketProgress),
                'approve'          => Gate::allows('approve', $ticket->ticketProgress),
                'reject'           => Gate::allows('reject', $ticket->ticketProgress),
                'assign'           => Gate::allows('assign', $ticket->ticketProgress),
                'markReady'        => Gate::allows('markReady', $ticket->ticketProgress),
                'reviewPpkh'       => Gate::allows('reviewPpkh', $ticket->ticketProgress),
                'forwardToBpkh'    => Gate::allows('forwardToBpkh', $ticket->ticketProgress),
                'requestRevision'  => Gate::allows('requestRevision', $ticket->ticketProgress)
                    || Gate::allows('requestRevisionBpkh', $ticket->ticketProgress),
                'finalApprove'     => Gate::allows('finalApprove', $ticket->ticketProgress),
                'finalize'         => Gate::allows('finalize', $ticket->ticketProgress),
            ],
        ]);
    }

    public function download(int $attachmentId)
    {
        $attachment = Attachment::with('ticketDetail')->findOrFail($attachmentId);

        $disk = Storage::disk('public');

        if (! $disk->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $ticket    = $attachment->ticketDetail;
        $ticketCode = $ticket->ticket_code;
        $pemohon    = str($ticket->name)->lower()->replace(' ', '-');

        $type = str_contains($attachment->file_path, 'surat_permohonan')
            ? 'surat-permohonan'
            : 'lampiran';

        $extension    = pathinfo($attachment->file_path, PATHINFO_EXTENSION);
        $downloadName = "{$ticketCode}_{$pemohon}_{$type}.{$extension}";

        return response()->download($disk->path($attachment->file_path), $downloadName);
    }

    public function showBeranda()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $visibleStatuses = TicketStatus::visibleForRole($role);

        $tickets = TicketDetail::query()
            ->with(['ticketProgress'])
            ->whereHas('ticketProgress', function ($q) use ($visibleStatuses) {
                $q->whereIn('status', array_map(fn($s) => $s->value, $visibleStatuses));
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($ticket) => [
                'ticket_code'        => $ticket->ticket_code,
                'name'               => $ticket->name,
                'status'             => $ticket->ticketProgress?->status?->label(),
                'current_assignment' => $ticket->ticketProgress?->current_assignment?->label(),
            ]);

        // Hitung distribusi berdasarkan status yang terlihat
        $distribution = [];
        foreach ($visibleStatuses as $status) {
            $distribution[] = [
                'label' => $status->label(),
                'value' => $status->value,
                'color' => $status->color(),
                'icon'  => $status->icon(),
                'count' => TicketDetail::query()
                    ->whereHas('ticketProgress', fn($q) => $q->where('status', $status->value))
                    ->count(),
            ];
        }

        return Inertia::render('Admin/Beranda/Beranda', [
            'tickets'      => $tickets,
            'distribution' => $distribution,
        ]);
    }

    public function dataPermohonan(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $visibleStatuses = TicketStatus::visibleForRole($role);

        $tickets = TicketDetail::query()
            ->with(['ticketProgress.latestAssignment'])
            ->whereHas('ticketProgress', function ($q) use ($visibleStatuses) {
                $q->whereIn('status', array_map(fn($s) => $s->value, $visibleStatuses));
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('ticket_code', 'like', "%{$request->search}%")
                        ->orWhere('name', 'like', "%{$request->search}%");
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->whereHas('ticketProgress', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            })
            ->when(
                $request->sort === 'oldest',
                fn($q) => $q->oldest(),
                fn($q) => $q->latest(),
            )
            ->paginate(10)
            ->through(fn($ticket) => [
                'ticket_code'        => $ticket->ticket_code,
                'name'               => $ticket->name,
                'is_read'            => $ticket->ticketProgress?->is_read ?? false,
                'status'             => $ticket->ticketProgress?->status?->label(),
                'status_value'       => $ticket->ticketProgress?->status?->value,
                'status_color'       => $ticket->ticketProgress?->status?->color(),
                'current_assignment' => $ticket->ticketProgress?->current_assignment?->label(),
                'date'               => $ticket->created_at->format('d M Y'),
            ]);

        return Inertia::render('Admin/DataPermohonan/Index', [
            'tickets'  => $tickets,
            'filters'  => $request->only('status', 'search', 'sort'),
            'userRole' => $role,
        ]);
    }
}
