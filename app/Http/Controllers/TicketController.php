<?php

namespace App\Http\Controllers;

use App\Models\TicketDetail;
use App\Models\Attachment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // Detail tiket untuk admin
    public function show(TicketDetail $ticket)
    {

        $ticket->load([
            'ticketProgress',
            'attachments',
            'ticketReplies'
        ]);

        $ticket->ticketProgress?->update(['is_read' => true]);

        $suratPermohonan = $ticket->attachments
            ->first(fn($in) => str_contains($in->file_path, 'surat_permohonan'));

        $lampiranLainnya = $ticket->attachments
            ->filter(fn($in) => !str_contains($in->file_path, 'lampiran_lainnya'))
            ->values();

        return Inertia::render('Admin/DataPermohonan/Detail/Show', [
            'ticket' => $ticket,
            'suratPermohonan' => $suratPermohonan,
            'lampiranLainnya' => $lampiranLainnya,
            'can' => [
                'verify'        => Gate::allows('verify', $ticket),
                'approve'       => Gate::allows('approve', $ticket),
                'reject'        => Gate::allows('reject', $ticket),
                'assign'        => Gate::allows('assign', $ticket),
                'markReady'     => Gate::allows('markReady', $ticket),
                'finalize'      => Gate::allows('finalize', $ticket),
            ],
        ]);
    }

    public function download(int $attachmentId)
    {
        $attachment = Attachment::with('ticketDetail')->findOrFail($attachmentId);

        // (opsional tapi sangat disarankan)
        // $this->authorize('view', $attachment->ticketDetail);

        $disk = Storage::disk('public');

        if (! $disk->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $ticket = $attachment->ticketDetail;

        $ticketCode = $ticket->ticket_code;
        $pemohon = str($ticket->name)
            ->lower()
            ->replace(' ', '-');

        $type = str_contains($attachment->file_path, 'surat_permohonan')
            ? 'surat-permohonan'
            : 'lampiran';

        $extension = pathinfo($attachment->file_path, PATHINFO_EXTENSION);

        $downloadName = "{$ticketCode}_{$pemohon}_{$type}.{$extension}";

        return response()->download($disk->path($attachment->file_path), $downloadName);
    }

    // Beranda admin dengan ringkasan tiket
    public function showBeranda()
    {
        $tickets = TicketDetail::query()
            ->with(['ticketProgress'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($ticket) => [
                'ticket_code' => $ticket->ticket_code,
                'name' => $ticket->name,
                'status' => $ticket->ticketProgress?->status?->label(),
            ]);

        return Inertia::render('Admin/Beranda/Beranda', [
            'tickets' => $tickets,
            'distribution' => [
                'Baru' => 30,
                'Proses' => 45,
                'Selesai' => 20,
                'Tolak' => 5,
            ],
        ]);
    }

    public function dataPermohonan(Request $request)
    {
        $tickets = TicketDetail::query()
            ->with(['ticketProgress'])
            ->when($request->search, function ($query) use ($request) {
                $query->where('ticket_code', 'like', "%{$request->search}%")
                    ->orWhere('name', 'like', "%{$request->search}%");
            })
            ->when($request->status, function ($query) use ($request) {
                $query->whereHas('ticketProgress', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            })
            ->when(
                $request->sort === 'oldest',
                fn($q) => $q->oldest(),
                fn($q) => $q->latest()
            )
            ->latest()
            ->paginate(10)
            ->through(fn($ticket) => [
                'ticket_code' => $ticket->ticket_code,
                'name'        => $ticket->name,
                'is_read'      => $ticket->ticketProgress?->is_read ?? false,
                'date'        => $ticket->created_at->format('d M Y'),
            ]);

        return Inertia::render('Admin/DataPermohonan/Index', [
            'tickets' => $tickets,
            'filters' => $request->only('status', 'search', 'sort'),
        ]);
    }
}
