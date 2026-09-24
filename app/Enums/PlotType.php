<?php

namespace App\Enums;

enum PlotType: string
{
    case Residential = 'residential';
    case Commercial = 'commercial';
    case Farmhouse = 'farmhouse';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badge(): string
    {
        return match ($this) {
            self::Residential => 'badge-navy',
            self::Commercial => 'badge-gold',
            self::Farmhouse => 'badge-success',
        };
    }
}
