<?php

namespace App\Services;

use App\Models\TicketProgress;
use Illuminate\Support\Facades\Auth;

class TicketActivityLogger
{
    public function log(
        TicketProgress $ticketProgress,
        string $action,
        string $description,
        array $properties = []
    ): void {
        $ticketProgress->loadMissing('ticketDetails');

        activity('ticket-workflow')
            ->performedOn($ticketProgress)
            ->causedBy(Auth::user())
            ->withProperties(array_merge([
                'ticket_code' => $ticketProgress->ticketDetails->ticket_code,
                'action' => $action,
                'from_status' => $ticketProgress->getOriginal('status'),
                'to_status' => $ticketProgress->status?->value,
                'current_assignment' => $ticketProgress->current_assignment?->value,
            ], $properties))
            ->log($description);
    }

    //log upload document
    public function logUploadDocument(TicketProgress $ticketProgress, string $fileName): void
    {
        $ticketProgress->loadMissing('ticketDetails');

        $this->log(
            $ticketProgress,
            'upload_document',
            "Document '{$fileName}' diupload untuk tiket #{$ticketProgress->ticketDetails->ticket_code} oleh " . Auth::user()->name . '.',
            ['file_name' => $fileName]
        );
    }
}
