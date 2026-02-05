<?php

namespace App\Enums;

enum SubmitMethod: string
{
    case DIRECT = 'direct';
    case INDIRECT = 'indirect';

    public function label(): string
    {
        return match ($this) {
            self::DIRECT => 'Langsung',
            self::INDIRECT => 'Tidak Langsung',
        };
    }
}
