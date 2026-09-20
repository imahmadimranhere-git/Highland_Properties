<?php

namespace App\Enums;

enum InstallmentFrequency: string
{
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    // Used by the installment calculator to convert a plan into a schedule.
    public function monthsPerInstallment(): int
    {
        return $this === self::Quarterly ? 3 : 1;
    }
}
