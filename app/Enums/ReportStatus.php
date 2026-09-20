<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Draft = 'draft';
    case Final = 'final';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badge(): string
    {
        return $this === self::Final ? 'badge-success' : 'badge-muted';
    }
}
