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
    public function __construct(
        private readonly N8nWebhookService $webhook,
    ) {}

    /**
     * Dipanggil setiap kali status tiket berubah.
     */
    public function onStatusChanged(
        TicketDetail $ticket,
        TicketStatus $fromStatus,
        TicketStatus $toStatus,
        ?string $notes = null
    ): void {
        // 1. Notifikasi ke pemohon (email + whatsapp)
        $applicantNotifications = $this->notifyApplicant($ticket, $fromStatus, $toStatus, $notes);

        // 2. Notifikasi ke petugas yang ditugaskan berikutnya
        $assignmentNotifications = $this->notifyNextAssignment($ticket, $toStatus, $notes);

        // 3. Dispatch semua ke n8n webhook secara real-time
        $allNotifications = array_merge($applicantNotifications, $assignmentNotifications);
        $this->webhook->dispatchBatch($allNotifications);
    }

    /**
     * Dipanggil saat tiket baru disubmit (dari RequestSubmissionService).
     */
    public function onTicketSubmitted(
        TicketDetail $ticket,
        User $user,
        ?string $plainPassword = null
    ): void {
        $notifications = [];

        // Email ke pemohon
        $notifications[] = NotificationLog::create([
            'type'              => 'ticket_submitted',
            'channel'           => 'email',
            'recipient_user_id' => $user->id,
            'recipient_email'   => $user->email,
            'recipient_phone'   => $ticket->telp,
            'ticket_detail_id'  => $ticket->id,
            'payload'           => [
                'ticket_code'    => $ticket->ticket_code,
                'name'           => $user->name,
                'email'          => $user->email,
                'plain_password' => $plainPassword,
                'is_new_user'    => $plainPassword !== null,
                'status'         => 'sent',
                'status_label'   => 'Dikirim',
                'login_url'      => url('/login'),
                'dashboard_url'  => url('/pemohon/hasil-permohonan'),
                'track_url'      => url('/pemohon/hasil-permohonan'),
                'message'        => "Permohonan {$ticket->ticket_code} berhasil dikirim. Silakan login ke dashboard pemohon untuk memantau proses permohonan Anda.",
            ],
        ]);

        // WhatsApp ke pemohon
        if ($ticket->telp) {
            $notifications[] = NotificationLog::create([
                'type'              => 'ticket_submitted',
                'channel'           => 'whatsapp',
                'recipient_user_id' => $user->id,
                'recipient_email'   => $user->email,
                'recipient_phone'   => $ticket->telp,
                'ticket_detail_id'  => $ticket->id,
                'payload'           => [
                    'ticket_code' => $ticket->ticket_code,
                    'name'        => $user->name,
                    'phone'       => $ticket->telp,
                    'status'      => 'sent',
                    'message'     => "Permohonan {$ticket->ticket_code} berhasil dikirim.",
                    'track_url'   => url('/lacak'),
                ],
            ]);
        }

        // ─── Notifikasi ke semua Admin TU: ada tiket baru masuk ───
        $adminNotifications = $this->notifyRoleUsers(
            role: 'admin_tu',
            type: 'new_ticket',
            ticket: $ticket,
            extra: [
                'status'       => 'sent',
                'status_label' => 'Dikirim',
                'submitted_by' => $user->name,
                'submitted_at' => now()->format('d M Y H:i'),
                'message'      => "Tiket baru {$ticket->ticket_code} dari {$user->name} memerlukan verifikasi Anda.",
            ]
        );
        $notifications = array_merge($notifications, $adminNotifications);

        // Dispatch semua ke n8n
        $this->webhook->dispatchBatch($notifications);
    }

    public function onAssignedToSpecificUser(
        TicketDetail $ticket,
        User $assignedUser,
        User $assignedBy,
        ?string $notes = null
    ): void {
        $notifications = [];

        // Email ke petugas seksi spesifik
        $notifications[] = NotificationLog::create([
            'type'              => 'assignment_notification',
            'channel'           => 'email',
            'recipient_user_id' => $assignedUser->id,
            'recipient_email'   => $assignedUser->email,
            'recipient_phone'   => null,
            'ticket_detail_id'  => $ticket->id,
            'payload'           => [
                'ticket_code'   => $ticket->ticket_code,
                'applicant'     => $ticket->name,
                'name'          => $assignedUser->name,
                'role'          => 'seksi',
                'assigned_by'   => $assignedBy->name,
                'status'        => TicketStatus::ASSIGNED->value,
                'status_label'  => TicketStatus::ASSIGNED->label(),
                'assignment'    => TicketAssignment::SEKSI->value,
                'notes'         => $notes,
                'message'       => "Tiket {$ticket->ticket_code} didisposisikan kepada Anda oleh {$assignedBy->name}.",
                'action_url'    => url("/tickets/{$ticket->ticket_code}"),
            ],
        ]);

        $this->webhook->dispatchBatch($notifications);
    }

    /**
     * Notifikasi ke pemohon tentang perubahan status.
     */
    private function notifyApplicant(
        TicketDetail $ticket,
        TicketStatus $fromStatus,
        TicketStatus $toStatus,
        ?string $notes
    ): array {
        $notifications = [];
        $message = $this->buildApplicantMessage($ticket, $toStatus, $notes);

        // Email
        $notifications[] = NotificationLog::create([
            'type'              => 'status_changed',
            'channel'           => 'email',
            'recipient_user_id' => $ticket->user_id,
            'recipient_email'   => $ticket->email,
            'recipient_phone'   => $ticket->telp,
            'ticket_detail_id'  => $ticket->id,
            'payload'           => [
                'ticket_code' => $ticket->ticket_code,
                'name'        => $ticket->name,
                'email'       => $ticket->email,
                'from_status' => $fromStatus->value,
                'from_label'  => $fromStatus->label(),
                'to_status'   => $toStatus->value,
                'to_label'    => $toStatus->label(),
                'notes'       => $notes,
                'message'     => $message,
                'login_url'   => url('/login'),
                'track_url'   => url('/lacak'),
            ],
        ]);

        // WhatsApp
        if ($ticket->telp) {
            $notifications[] = NotificationLog::create([
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

        return $notifications;
    }

    /**
     * Notifikasi ke petugas yang ditugaskan berikutnya.
     */
    private function notifyNextAssignment(
        TicketDetail $ticket,
        TicketStatus $toStatus,
        ?string $notes
    ): array {
        if ($toStatus === TicketStatus::ASSIGNED) {
            return [];
        }
        $nextAssignment = TicketAssignment::nextAssignmentForStatus($toStatus);
        if (!$nextAssignment) return [];

        $roleName = $this->mapAssignmentToRole($nextAssignment);
        if (!$roleName) return [];

        return $this->notifyRoleUsers(
            role: $roleName,
            type: 'assignment_notification',
            ticket: $ticket,
            extra: [
                'status'       => $toStatus->value,
                'status_label' => $toStatus->label(),
                'assignment'   => $nextAssignment->value,
                'notes'        => $notes,
                'message'      => $this->buildAssignmentMessage($ticket, $toStatus, $nextAssignment),
            ]
        );
    }

    /**
     * Buat NotificationLog untuk semua user dengan role tertentu.
     */
    private function notifyRoleUsers(
        string $role,
        string $type,
        TicketDetail $ticket,
        array $extra = []
    ): array {
        $notifications = [];
        $users = User::role($role)->get();

        foreach ($users as $user) {
            $notifications[] = NotificationLog::create([
                'type'              => $type,
                'channel'           => 'email',
                'recipient_user_id' => $user->id,
                'recipient_email'   => $user->email,
                'recipient_phone'   => null,
                'ticket_detail_id'  => $ticket->id,
                'payload'           => array_merge([
                    'ticket_code' => $ticket->ticket_code,
                    'applicant'   => $ticket->name,
                    'role'        => $role,
                    'message'     => "Tiket {$ticket->ticket_code} memerlukan tindakan Anda.",
                    'action_url'  => url("/tickets/{$ticket->ticket_code}"),
                ], $extra),
            ]);
        }

        return $notifications;
    }

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

    private function buildApplicantMessage(TicketDetail $ticket, TicketStatus $status, ?string $notes): string
    {
        $code = $ticket->ticket_code;

        return match ($status) {
            TicketStatus::SENT              => "Permohonan {$code} berhasil dikirim. Menunggu verifikasi.",
            TicketStatus::VERIFIED          => "Permohonan {$code} telah diverifikasi. Menunggu persetujuan pimpinan.",
            TicketStatus::APPROVED          => "Permohonan {$code} telah disetujui pimpinan. Sedang didisposisikan.",
            TicketStatus::ASSIGNED          => "Permohonan {$code} telah didisposisikan ke seksi terkait.",
            TicketStatus::READY             => "Data permohonan {$code} telah siap dan sedang direview.",
            TicketStatus::UNDER_REVIEW_PPKH => "Permohonan {$code} sedang direview oleh Pimpinan PPKH.",
            TicketStatus::UNDER_REVIEW_BPKH => "Permohonan {$code} sedang direview oleh Pimpinan BPKH.",
            TicketStatus::REVISION          => "Permohonan {$code} memerlukan revisi." . ($notes ? " Catatan: {$notes}" : ''),
            TicketStatus::FINAL_APPROVED    => "Permohonan {$code} telah mendapat persetujuan akhir. Sedang difinalisasi.",
            TicketStatus::COMPLETED         => "Permohonan {$code} telah selesai! Silakan login untuk mengunduh hasil data.",
            TicketStatus::REJECTED          => "Permohonan {$code} ditolak." . ($notes ? " Alasan: {$notes}" : ''),
        };
    }

    /**
     * Pesan untuk petugas berdasarkan status dan assignment berikutnya.
     */
    private function buildAssignmentMessage(
        TicketDetail $ticket,
        TicketStatus $status,
        TicketAssignment $assignment
    ): string {
        $code = $ticket->ticket_code;
        $applicant = $ticket->name;

        return match ($status) {
            TicketStatus::SENT              => "Tiket baru {$code} dari {$applicant} memerlukan verifikasi Anda.",
            TicketStatus::VERIFIED          => "Tiket {$code} telah diverifikasi dan memerlukan persetujuan Anda.",
            TicketStatus::APPROVED          => "Tiket {$code} telah disetujui. Silakan disposisikan ke seksi terkait.",
            TicketStatus::READY             => "Data tiket {$code} telah disiapkan seksi. Silakan review.",
            TicketStatus::UNDER_REVIEW_PPKH => "Tiket {$code} sedang menunggu review Anda (PPKH).",
            TicketStatus::UNDER_REVIEW_BPKH => "Tiket {$code} telah direview PPKH. Silakan review final.",
            TicketStatus::REVISION          => "Tiket {$code} memerlukan revisi. Silakan perbaiki data.",
            TicketStatus::FINAL_APPROVED    => "Tiket {$code} telah disetujui final. Silakan selesaikan dan serahkan ke pemohon.",
            default                         => "Tiket {$code} memerlukan tindakan Anda.",
        };
    }
}
