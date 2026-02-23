<?php

namespace App\Enums;

enum TicketAssignment: string
{
    case ADMIN_TU = 'admin_tu';
    case SEKSI = 'seksi';
    case PIMPINAN_PPKH = 'pimpinan_ppkh';
    case PIMPINAN_BPKH = 'pimpinan_bpkh';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN_TU => 'Admin TU',
            self::SEKSI  => 'Seksi',
            self::PIMPINAN_PPKH  => 'Pimpinan PPKH',
            self::PIMPINAN_BPKH => 'Pimpinan BPKH',
        };
    }

    public function correspondingRole(): string
    {
        return $this->value;
    }

    /**
     * Target assignment berikutnya berdasarkan status saat ini.
     */
    public static function nextAssignmentForStatus(TicketStatus $status): ?self
    {
        return match ($status) {
            TicketStatus::VERIFIED           => self::PIMPINAN_BPKH,
            TicketStatus::APPROVED           => self::PIMPINAN_PPKH,
            TicketStatus::UNDER_REVIEW_PPKH  => self::PIMPINAN_BPKH,
            TicketStatus::UNDER_REVIEW_BPKH  => self::ADMIN_TU,
            TicketStatus::READY              => self::PIMPINAN_PPKH,
            TicketStatus::REVISION           => self::SEKSI,
            TicketStatus::FINAL_APPROVED     => self::ADMIN_TU,
            default                          => null,
        };
    }
}
