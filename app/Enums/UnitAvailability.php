<?php

namespace App\Enums;

enum UnitAvailability: string
{
    case Available = 'available';
    case Limited = 'limited';
    case SoldOut = 'sold_out';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Limited => 'Limited',
            self::SoldOut => 'Sold Out',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Available => 'badge-success',
            self::Limited => 'badge-gold',
            self::SoldOut => 'badge-muted',
        };
    }
}
