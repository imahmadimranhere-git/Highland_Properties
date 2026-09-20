<?php

namespace App\Enums;

enum OwnershipFlag: string
{
    case Marketed = 'marketed';
    case OwnDevelopment = 'own_development';

    public function label(): string
    {
        return match ($this) {
            self::Marketed => 'Marketed',
            self::OwnDevelopment => 'Own Development',
        };
    }
}
