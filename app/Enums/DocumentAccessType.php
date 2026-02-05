<?php

namespace App\Enums;

enum DocumentAccessType: string
{
    case COPY = 'copy';
    case VIEW_ONLY = 'view_only';

    public function label(): string
    {
        return match ($this) {
            self::COPY => 'Salinan (Softcopy / Hardcopy)',
            self::VIEW_ONLY => 'Melihat / Membaca / Mencatat',
        };
    }
}
