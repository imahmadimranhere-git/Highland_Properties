<?php

namespace App\Enums;

enum AreaUnit: string
{
    case Marla = 'marla';
    case Kanal = 'kanal';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /** The whole point of this enum: 1 Kanal = 20 Marla. */
    public function inMarla(float $value): float
    {
        return $this === self::Kanal ? $value * 20 : $value;
    }
}
