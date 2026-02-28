<?php

namespace App\Http\Controllers;

use App\Models\TicketDetail;
use App\Models\Attachment;
use App\Models\Seksi;
use Spatie\Activitylog\Models\Activity;
use App\Enums\TicketStatus;
use App\Services\DashboardActivityService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;


class TicketController extends Controller
{
    public function __construct(
        private readonly DashboardActivityService $activityService,
    ) {}

    public function show(TicketDetail $ticket)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        if ($role === 'seksi') {
            $isAssigned = $ticket->ticketProgress?->assignments()
                ->where('assigned_to_user_id', $user->id)
                ->exists();

            if (! $isAssigned) {
                abort(403, 'Anda tidak memiliki akses ke tiket ini.');
            }
        }

        $ticket->load([
            'ticketProgress.assignments.assignedByUser',
            'ticketProgress.assignments.assignedToUser',
            'ticketProgress.assignments.seksi',
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
        $activities = Activity::query()
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

        $seksiList = [];
        if (Gate::allows('assign', $ticket->ticketProgress)) {
            $seksiList = Seksi::with(['petugasSeksi' => function ($query) {
                $query->select('id', 'name', 'email', 'seksi_id');
            }])
                ->get()
                ->map(fn($seksi) => [
                    'id' => $seksi->id,
                    'nama' => $seksi->name,
                    'users' => $seksi->petugasSeksi->map(fn($petugas) => [
                        'id' => $petugas->id,
                        'name' => $petugas->name,
                        'email' => $petugas->email,
                    ]),
                ]);
        }

        return Inertia::render('Admin/DataPermohonan/Detail/Show', [
            'ticket'           => $ticket,
            'suratPermohonan'  => $suratPermohonan,
            'lampiranLainnya'  => $lampiranLainnya,
            'is_read'          => $is_read,
            'activities'       => $activities,
            'seksiList'       => $seksiList,
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

        // Distribusi status
        $distribution = [];
        foreach ($visibleStatuses as $status) {
            $count = TicketDetail::query()
                ->whereHas('ticketProgress', fn($q) => $q->where('status', $status->value))
                ->when($role === 'seksi', function ($query) use ($user) {
                    $query->whereHas('ticketProgress.assignments', function ($q) use ($user) {
                        $q->where('assigned_to_user_id', $user->id);
                    });
                })
                ->count();

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

        // Riwayat aktivitas terakhir (delegasi ke service)
        $recentActivities = $this->activityService->recentActivities($role, 10);

        return Inertia::render('Admin/Beranda/Beranda', [
            'distribution'     => $distribution,
            'recentActivities' => $recentActivities,
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

            ->when($role === 'seksi', function ($query) use ($user) {
                $query->whereHas('ticketProgress.assignments', function ($q) use ($user) {
                    $q->where('assigned_to_user_id', $user->id);
                });
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

    public function destroy(TicketDetail $ticket)
    {
        DB::transaction(function () use ($ticket) {
            // Hapus file lampiran dari storage
            foreach ($ticket->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // Hapus relasi terkait
            $ticket->attachments()->delete();
            $ticket->ticketReplies()->delete();
            $ticket->feedbacks()->delete();

            if ($ticket->ticketProgress) {
                $ticket->ticketProgress->assignments()->delete();
                $ticket->ticketProgress()->delete();
            }

            $ticket->delete();
        });

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'Tiket berhasil dihapus.');
    }

    /**
     * Pimpinan PPKH → Disposisi ke Seksi (UNDER_REVIEW_PPKH → ASSIGNED)
     */
    public function confirmDisposition(TicketDetail $ticket)
    {
        $ticket->load('ticketProgress');

        Gate::authorize('confirmDisposition', $ticket->ticketProgress);

        $ticket->ticketProgress->update([
            'status' => TicketStatus::ASSIGNED->value,
            'current_assignment' => 'Seksi',
        ]);

        return back()->with('success', 'Disposisi berhasil dikonfirmasi.');
    }
}
