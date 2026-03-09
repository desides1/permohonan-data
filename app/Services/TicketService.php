<?php

namespace App\Services;

use App\Models\DocumentDrive;
use App\Models\TicketDetail;
use App\Services\GoogleDriveService;
use App\Services\TicketActivityLogger;
use Illuminate\Http\UploadedFile;


class TicketService
{

    public function __construct(
        private readonly GoogleDriveService $googleDriveService,
        private readonly TicketActivityLogger $logger,
    ) {}

    public function uploadResultFile(TicketDetail $ticket, UploadedFile $file, ?string $keterangan =  null): DocumentDrive
    {
        $folderId = $this->ensureTicketFolder($ticket);

        $filename = time() . '_' . $file->getClientOriginalName();

        $driveFiledId = $this->googleDriveService->uploadFile(
            $file,
            $filename,
            $folderId
        );

        $document = $ticket->documents()->create([
            'drive_file_id' => $driveFiledId,
            'drive_folder_id' => $folderId,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'keterangan' => $keterangan,
            'uploaded_by' => auth()->id(),
        ]);

        if ($ticket->ticketProgress) {
            $this->logger->logUploadDocument($ticket->ticketProgress, $file->getClientOriginalName());
        }

        return $document;
    }

    public function uploadMultipleFiles(TicketDetail $ticket, array $files, ?string $keterangan = null): array
    {
        $document = [];

        foreach ($files as $file) {
            $document[] = $this->uploadResultFile($ticket, $file, $keterangan);
        }

        return $document;
    }

    public function deleteDocument(DocumentDrive $document): void
    {
        try {
            $this->googleDriveService->deleteFile($document->drive_file_id);
        } catch (\Exception $e) {
            report($e);
        }

        $document->delete();
    }

    public function downloadDocument($document): \Illuminate\Http\Response
    {
        $fileContent = $this->googleDriveService->downloadFile($document->drive_file_id);
        $meta = $this->googleDriveService->getFileMetadata($document->drive_file_id);

        return response($fileContent, 200)
            ->header('Content-Type', $meta->mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $document->original_name . '"');
    }

    private function ensureTicketFolder(TicketDetail $ticket): string
    {
        $yearFolder = $this->googleDriveService->createFolderIfNotExists(now()->format('Y'));
        $monthFolder = $this->googleDriveService->createFolderIfNotExists(now()->format('m'), $yearFolder);
        $ticketFolder = $this->googleDriveService->createFolderIfNotExists('ticket-' . $ticket->ticket_code, $monthFolder);

        return $ticketFolder;
    }
}
