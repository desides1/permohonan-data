<?php

namespace App\Services;

use App\Models\TicketDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\TicketProgress;
use App\Services\AttachmentUploader;
use App\Services\ProgressRecorder;
use App\Services\PemohonAutoRegisterService;
use App\Services\TicketNotificationService;
use App\Models\NotificationLog;
use App\Models\User;

class RequestSubmissionService
{
    public function __construct(
        private PemohonAutoRegisterService $autoRegister,
        private TicketNotificationService $notificationService,
    ) {}

    public function submit(array $data): TicketDetail
    {
        return DB::transaction(function () use ($data) {
            // Auto-register pemohon jika belum ada
            [$user, $plainPassword] = $this->autoRegister->findOrRegister($data);

            // Buat tiket baru
            $detail = TicketDetail::create([
                ...$data,
                'user_id' => $user->id,
                'ticket_code' => $this->generateTicketCode(),
            ]);

            // Handle attachment jika ada
            app(AttachmentUploader::class)->handle($detail, $data);

            // Record progress awal
            app(ProgressRecorder::class)->record(
                $detail,
                'sent',
                'admin_tu'
            );

            // 5. Trigger notifikasi + webhook ke n8n (real-time)
            $this->notificationService->onTicketSubmitted($detail, $user, $plainPassword);

            NotificationLog::create([
                'type'              => 'ticket_submitted',
                'channel'           => 'email',
                'recipient_user_id' => $user->id,
                'recipient_email'   => $user->email,
                'recipient_phone'   => $data['telp'] ?? null,
                'ticket_detail_id'  => $detail->id,
                'payload'           => [
                    'ticket_code'    => $detail->ticket_code,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'plain_password' => $plainPassword, // null jika user sudah ada
                    'is_new_user'    => $plainPassword !== null,
                    'status'         => 'sent',
                    'status_label'   => 'Dikirim',
                ],
                'sent_at'           => null,
            ]);

            $this->notifyRoleUsers('admin_tu', 'new_ticket', $detail);

            return $detail;
        });
    }

    private function notifyRoleUsers(string $role, string $type, TicketDetail $detail): void
    {
        $users = User::role($role)->get();

        foreach ($users as $user) {
            NotificationLog::create([
                'type'              => $type,
                'channel'           => 'email',
                'recipient_user_id' => $user->id,
                'recipient_email'   => $user->email,
                'recipient_phone'   => $user->telp ?? null,
                'ticket_detail_id'  => $detail->id,
                'payload'           => [
                    'ticket_code'  => $detail->ticket_code,
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'status'       => 'sent',
                    'status_label' => 'Dikirim',
                    'message'      => "Tiket baru dengan kode {$detail->ticket_code} telah diajukan dan menunggu verifikasi.",
                ],
                'sent_at'           => null,
            ]);
        }
    }

    /**
     * Generate kode tiket singkat (5 karakter)
     * Contoh: DT-042
     */
    // public function submit(array $data): TicketDetail
    // {
    //     return DB::transaction(function () use ($data) {


    //         $detail = TicketDetail::create([
    //             ...$data,
    //             'ticket_code' => $this->generateTicketCode(),
    //         ]);

    //         app(AttachmentUploader::class)
    //             ->handle($detail, $data);

    //         app(ProgressRecorder::class)
    //             ->record(
    //                 $detail,
    //                 'sent',
    //                 'admin_tu'
    //             );

    //         return $detail;
    //     });
    // }

    /**
     * Generate kode tiket singkat (5 karakter)
     * Contoh: A9F3K
     */
    private function generateTicketCode(): string
    {
        return 'DT-' . str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
    }
}
