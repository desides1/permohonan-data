<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketDetail;
use App\Models\TicketReply;
use App\Models\DocumentDrive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DataRequestResultController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        try {
            $tickets = TicketDetail::where('user_id', $user->id)
                ->with(['ticketProgress', 'ticketReplies.user', 'feedbacks', 'documents', 'attachments'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($ticket) {
                    return $this->formatTicket($ticket);
                });
        } catch (\Exception $e) {
            Log::error('DataRequestResult index error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            $tickets = collect();
        }

        return Inertia::render('Pemohon/DataRequestResult', [
            'tickets' => $tickets,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();

        $ticket = TicketDetail::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['ticketProgress', 'ticketReplies.user', 'feedbacks', 'documents', 'attachments'])
            ->firstOrFail();

        $ticketData = $this->formatTicket($ticket);

        return Inertia::render('Pemohon/DataRequestResult', [
            'ticket' => $ticketData,
            'tickets' => [$ticketData],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function downloadDocument($id)
    {
        $user = Auth::user();

        $document = DocumentDrive::findOrFail($id);

        // Verifikasi kepemilikan
        TicketDetail::where('id', $document->ticket_detail_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $path = $document->file_path;

        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($path, $document->original_name ?? $document->name);
    }

    public function downloadResult($ticketId)
    {
        $user = Auth::user();

        $ticket = TicketDetail::where('id', $ticketId)
            ->where('user_id', $user->id)
            ->with('ticketProgress')
            ->firstOrFail();

        // Cek status completed
        if ($ticket->ticketProgress?->status?->value !== 'completed') {
            return back()->with('error', 'Permohonan belum selesai.');
        }

        // Ambil dokumen hasil dari DocumentDrive
        $resultDoc = $ticket->documents()->where('type', 'hasil')->first();

        if (!$resultDoc || !Storage::disk('public')->exists($resultDoc->file_path)) {
            return back()->with('error', 'File hasil belum tersedia.');
        }

        return Storage::disk('public')->download(
            $resultDoc->file_path,
            $resultDoc->original_name ?? 'hasil-permohonan.pdf'
        );
    }

    /**
     * Format ticket data untuk response — sesuai struktur model sebenarnya.
     */
    private function formatTicket(TicketDetail $ticket): array
    {
        $progress = $ticket->ticketProgress;
        $statusValue = $progress?->status?->value ?? 'sent';
        $statusLabel = $progress?->status?->label() ?? 'Dikirim';

        return [
            'id'              => $ticket->id,
            'ticket_number'   => $ticket->ticket_code,
            'applicant_name'  => $ticket->name,
            'email'           => $ticket->email,
            'phone'           => $ticket->telp,
            'job'             => $ticket->job,
            'institute'       => $ticket->institute,
            'request_type'    => $ticket->data_purpose,
            'description'     => $ticket->details_data ?? '-',
            'status'          => $statusValue,
            'status_label'    => $statusLabel,
            'assignment'      => $progress?->current_assignment?->value,
            'assignment_label' => $progress?->current_assignment?->label(),
            'notes'           => $progress?->notes,
            'created_at'      => $ticket->created_at?->format('d M Y, H:i') ?? '-',
            'updated_at'      => $ticket->updated_at?->format('d M Y, H:i') ?? '-',

            'documents' => $ticket->documents ? $ticket->documents->map(function ($doc) {
                return [
                    'id'          => $doc->id,
                    'name'        => $doc->original_name ?? $doc->name ?? 'Dokumen',
                    'type'        => $doc->type ?? 'Dokumen',
                    'status'      => $doc->status ?? 'uploaded',
                    'file_path'   => $doc->file_path,
                    'uploaded_at' => $doc->created_at?->format('d M Y, H:i') ?? '-',
                ];
            }) : [],

            'attachments' => $ticket->attachments ? $ticket->attachments->map(function ($att) {
                return [
                    'id'          => $att->id,
                    'name'        => $att->original_name ?? $att->file_name ?? 'Lampiran',
                    'type'        => $att->type ?? 'Lampiran',
                    'file_path'   => $att->file_path,
                    'uploaded_at' => $att->created_at?->format('d M Y, H:i') ?? '-',
                ];
            }) : [],

            'replies' => $ticket->ticketReplies ? $ticket->ticketReplies->map(function ($reply) {
                return [
                    'id'         => $reply->id,
                    'content'    => $reply->content,
                    'replied_by' => $reply->user->name ?? 'Petugas',
                    'role'       => $reply->user?->getRoleNames()?->first() ?? 'admin',
                    'created_at' => $reply->created_at?->format('d M Y, H:i') ?? '-',
                ];
            }) : [],

            'has_feedback' => $ticket->feedbacks !== null,
            'has_result'   => $ticket->documents?->where('type', 'hasil')->isNotEmpty() ?? false,
        ];
    }
}
