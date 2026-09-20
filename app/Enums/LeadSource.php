<?php

namespace App\Enums;

enum LeadSource: string
{
    case Website = 'website';
    case Manual = 'manual';
    case Call = 'call';
    case Whatsapp = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'Website',
            self::Manual => 'Manual Entry',
            self::Call => 'Phone Call',
            self::Whatsapp => 'WhatsApp',
        };
    }
}
