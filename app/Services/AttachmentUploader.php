<?php

namespace App\Services;

use App\Models\TicketDetail;
use Illuminate\Http\UploadedFile;

class AttachmentUploader
{
    public function handle(TicketDetail $detail, array $data): void
    {
        if (!empty($data['surat_permohonan'])) {
            $this->store($detail, $data['surat_permohonan'], 'surat_permohonan');
        }

        foreach ($data['lampiran'] ?? [] as $file) {
            $this->store($detail, $file, 'lampiran');
        }
    }

    private function store(TicketDetail $detail, UploadedFile $file, string $type): void
    {
        $path = $file->store("attachments/{$type}", 'public');

        $detail->attachments()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
        ]);
    }
}
