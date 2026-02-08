<?php

namespace App\Services;

use App\Models\TicketDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\TicketProgress;
use App\Services\AttachmentUploader;
use App\Services\ProgressRecorder;

class RequestSubmissionService
{
    public function submit(array $data): TicketDetail
    {
        return DB::transaction(function () use ($data) {


            $detail = TicketDetail::create([
                ...$data,
                'ticket_code' => $this->generateTicketCode(),
            ]);

            app(AttachmentUploader::class)
                ->handle($detail, $data);

            app(ProgressRecorder::class)
                ->record(
                    $detail,
                    'sent',
                    'admin_tu'
                );

            return $detail;
        });
    }

    /**
     * Generate kode tiket singkat (5 karakter)
     * Contoh: A9F3K
     */
    private function generateTicketCode(): string
    {
        return 'DT-' . str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
    }
}
