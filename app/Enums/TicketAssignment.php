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
}
