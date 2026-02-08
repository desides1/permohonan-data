<?php

namespace App\Enums;

enum DeliveryMethod: string
{
    case LANGSUNG = 'langsung';
    case EMAIL = 'email';
    case POST = 'post';
    case COURIER = 'courier';

    public function label(): string
    {
        return match ($this) {
            self::LANGSUNG => 'Langsung',
            self::EMAIL => 'Email',
            self::POST => 'Pos',
            self::COURIER => 'Kurir',
        };
    }
}
