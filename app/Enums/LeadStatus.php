<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case SiteVisit = 'site_visit';
    case Negotiation = 'negotiation';
    case ClosedWon = 'closed_won';
    case ClosedLost = 'closed_lost';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::SiteVisit => 'Site Visit',
            self::Negotiation => 'Negotiation',
            self::ClosedWon => 'Closed (Won)',
            self::ClosedLost => 'Closed (Lost)',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::New => 'badge-navy',
            self::Contacted, self::SiteVisit, self::Negotiation => 'badge-gold',
            self::ClosedWon => 'badge-success',
            self::ClosedLost => 'badge-danger',
        };
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::ClosedWon, self::ClosedLost], true);
    }
}
