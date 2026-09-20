<?php

namespace App\Enums;

enum TestimonialStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'badge-gold',
            self::Approved => 'badge-success',
            self::Rejected => 'badge-danger',
        };
    }
}
