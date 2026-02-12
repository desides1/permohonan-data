<?php

namespace App\Services;

use App\Models\TicketDetail;

class ProgressRecorder
{
    public function record(
        TicketDetail $detail,
        string $status,
        ?string $note = null
    ): void {
        $detail->ticketProgress()->create([
            'ticket_details_id' => $detail->id,
            'status' => $status,
            'note' => $note,
        ]);
    }
}
