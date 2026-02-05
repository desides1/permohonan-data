<?php

namespace App\Enums;

enum TicketStatus: string
{
    case SENT = 'sent';
    case VERIFIED = 'verified';
    case APPROVED = 'approved';
    case ASSIGNED = 'assigned';
    case READY = 'ready';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::SENT => 'Dikirim',
            self::VERIFIED => 'Diverifikasi Admin TU',
            self::APPROVED => 'Disetujui Pimpinan',
            self::ASSIGNED => 'Ditugaskan',
            self::READY => 'Data Siap',
            self::COMPLETED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }
}
