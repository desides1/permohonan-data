<?php

namespace App\Services;

use App\Enums\TicketAssignment;
use App\Enums\TicketStatus;
use App\Models\NotificationLog;
use App\Models\TicketDetail;
use App\Models\TicketProgress;
use App\Models\User;

class TicketNotificationService
{
    /**
     * Dipanggil setiap kali status tiket berubah.
     * Membuat NotificationLog untuk pemohon + petugas yang relevan.
     */
    public function onStatusChanged(
        TicketDetail $ticket,
        TicketStatus $fromStatus,
        TicketStatus $toStatus,
        ?string $notes = null
    ): void {
        // 1. Notifikasi ke pemohon
        $this->notifyApplicant($ticket, $fromStatus, $toStatus, $notes);

        // 2. Notifikasi ke petugas yang ditugaskan berikutnya
        $nextAssignment = TicketAssignment::nextAssignmentForStatus($toStatus);
        if ($nextAssignment) {
            $this->notifyAssignedRole($ticket, $toStatus, $nextAssignment, $notes);
        }
    }

    /**
     * Notifikasi ke pemohon tentang perubahan status.
     */
    private function notifyApplicant(
        TicketDetail $ticket,
        TicketStatus $fromStatus,
        TicketStatus $toStatus,
        ?string $notes
    ): void {
        $message = $this->buildApplicantMessage($ticket, $toStatus, $notes);

        // Email
        NotificationLog::create([
            'type'              => 'status_changed',
            'channel'           => 'email',
            'recipient_user_id' => $ticket->user_id,
            'recipient_email'   => $ticket->email,
            'recipient_phone'   => $ticket->telp,
            'ticket_detail_id'  => $ticket->id,
            'payload'           => [
                'ticket_code'    => $ticket->ticket_code,
                'name'           => $ticket->name,
                'email'          => $ticket->email,
                'from_status'    => $fromStatus->value,
                'from_label'     => $fromStatus->label(),
                'to_status'      => $toStatus->value,
                'to_label'       => $toStatus->label(),
                'notes'          => $notes,
                'message'        => $message,
                'login_url'      => url('/login'),
                'track_url'      => url('/lacak'),
            ],
        ]);

        // WhatsApp (duplikat dengan channel berbeda)
        if ($ticket->telp) {
            NotificationLog::create([
                'type'              => 'status_changed',
                'channel'           => 'whatsapp',
                'recipient_user_id' => $ticket->user_id,
                'recipient_email'   => $ticket->email,
                'recipient_phone'   => $ticket->telp,
                'ticket_detail_id'  => $ticket->id,
                'payload'           => [
                    'ticket_code' => $ticket->ticket_code,
                    'name'        => $ticket->name,
                    'phone'       => $ticket->telp,
                    'to_status'   => $toStatus->value,
                    'to_label'    => $toStatus->label(),
                    'notes'       => $notes,
                    'message'     => $message,
                ],
            ]);
        }
    }

    /**
     * Notifikasi ke petugas berdasarkan role yang ditugaskan.
     */
    private function notifyAssignedRole(
        TicketDetail $ticket,
        TicketStatus $toStatus,
        TicketAssignment $assignment,
        ?string $notes
    ): void {
        $roleName = $this->mapAssignmentToRole($assignment);
        if (!$roleName) return;

        $users = User::role($roleName)->get();

        foreach ($users as $user) {
            NotificationLog::create([
                'type'              => 'assignment_notification',
                'channel'           => 'email',
                'recipient_user_id' => $user->id,
                'recipient_email'   => $user->email,
                'recipient_phone'   => null,
                'ticket_detail_id'  => $ticket->id,
                'payload'           => [
                    'ticket_code'  => $ticket->ticket_code,
                    'applicant'    => $ticket->name,
                    'status'       => $toStatus->value,
                    'status_label' => $toStatus->label(),
                    'role'         => $roleName,
                    'assignment'   => $assignment->value,
                    'notes'        => $notes,
                    'message'      => "Tiket {$ticket->ticket_code} memerlukan tindakan Anda ({$toStatus->label()}).",
                    'action_url'   => url("/tickets/{$ticket->ticket_code}"),
                ],
            ]);
        }
    }

    /**
     * Mapping TicketAssignment enum ke role Spatie.
     */
    private function mapAssignmentToRole(TicketAssignment $assignment): ?string
    {
        return match ($assignment) {
            TicketAssignment::ADMIN_TU      => 'admin_tu',
            TicketAssignment::PIMPINAN_BPKH => 'pimpinan_bpkh',
            TicketAssignment::PIMPINAN_PPKH => 'pimpinan_ppkh',
            TicketAssignment::SEKSI         => 'seksi',
            default                         => null,
        };
    }

    /**
     * Pesan untuk pemohon berdasarkan status.
     */
    private function buildApplicantMessage(TicketDetail $ticket, TicketStatus $status, ?string $notes): string
    {
        $code = $ticket->ticket_code;

        return match ($status) {
            TicketStatus::SENT             => "Permohonan {$code} berhasil dikirim. Menunggu verifikasi.",
            TicketStatus::VERIFIED         => "Permohonan {$code} telah diverifikasi. Menunggu persetujuan pimpinan.",
            TicketStatus::APPROVED         => "Permohonan {$code} telah disetujui pimpinan. Sedang didisposisikan.",
            TicketStatus::ASSIGNED         => "Permohonan {$code} telah didisposisikan ke seksi terkait.",
            TicketStatus::READY            => "Data permohonan {$code} telah siap dan sedang direview.",
            TicketStatus::UNDER_REVIEW_PPKH => "Permohonan {$code} sedang direview oleh Pimpinan PPKH.",
            TicketStatus::UNDER_REVIEW_BPKH => "Permohonan {$code} sedang direview oleh Pimpinan BPKH.",
            TicketStatus::REVISION         => "Permohonan {$code} memerlukan revisi." . ($notes ? " Catatan: {$notes}" : ''),
            TicketStatus::FINAL_APPROVED   => "Permohonan {$code} telah mendapat persetujuan akhir. Sedang difinalisasi.",
            TicketStatus::COMPLETED        => "Permohonan {$code} telah selesai! Silakan login untuk mengunduh hasil data.",
            TicketStatus::REJECTED         => "Permohonan {$code} ditolak." . ($notes ? " Alasan: {$notes}" : ''),
            default                        => "Status permohonan {$code} diperbarui menjadi: {$status->label()}.",
        };
    }
}