<?php

namespace App\Http\Controllers;

use App\Models\TicketDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Ticket;
use App\Models\Attachment;


class RequestController extends Controller
{
    public function showRequest()
    {
        return Inertia::render('LandingPage/Form');
    }

    public function postRequest(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telp' => 'required|string|max:20',
            'job' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'institute' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'submit_data' => 'required|string',
            'get_doc' => 'required|string',
            'send_doc' => 'required|string',
            'data_purpose' => 'required|string|max:500',
            'details_data' => 'nullable|string|max:1000',
            'surat_permohonan' => 'required|file|mimes:pdf|max:2048',
            'lampiran.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5048',
        ]);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'telp' => $request->input('telp'),
            'job' => $request->input('job'),
            'postal_code' => $request->input('postal_code'),
            'institute' => $request->input('institute'),
            'address' => $request->input('address'),
            'submit_data' => $request->input('submit_data'),
            'get_doc' => $request->input('get_doc'),
            'send_doc' => $request->input('send_doc'),
            'data_purpose' => $request->input('data_purpose'),
            'details_data' => $request->input('details_data'),
        ];

        $requestData = TicketDetail::create($data);

        $fileSuratPermohonan = $request->file('surat_permohonan');
        $fileLampiran = $request->file('lampiran', []);

        if ($fileSuratPermohonan) {
            $suratPath = $fileSuratPermohonan->store('attachments/surat_permohonan', 'public');
            $requestData->attachments()->create([
                'ticket_details_id' => $requestData->id,
                'file_name' => $fileSuratPermohonan->getClientOriginalName(),
                'file_path' => $suratPath,
                'type' => 'surat_permohonan',
            ]);
        }

        if (is_array($fileLampiran)) {
            foreach ($fileLampiran as $file) {
                if ($file) {
                    $lampiranPath = $file->store('attachments/lampiran', 'public');
                    $requestData->attachments()->create([
                        'ticket_details_id' => $requestData->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $lampiranPath,
                        'type' => 'lampiran',
                    ]);
                }
            }
        }

        $ticket = $requestData->ticket()->create([
            'ticket_code' => 'DT' . random_int(100, 999),
            'status' => 'Dikirim',
            'assignment' => 'Admin TU',
        ]);

        return redirect()->back()->with('ticket_code', $ticket->ticket_code)->with('success', 'Permohonan berhasil dikirim. Silakan catat kode tiket Anda.');
    }

    public function trackTicket(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
        ]);

        $ticket = Ticket::where('ticket_code', $request->ticket_code)->with('detail')->first();

        if (!$ticket) {
            return redirect()->back()->with('ticket', $ticket)->with('error', 'Kode tiket tidak ditemukan.');
        }

        return Inertia::render('LandingPage/TrackRequest', [
            'ticket' => $ticket,
        ]);
    }
}
