<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Upcoming = 'upcoming';
    case ForSale = 'for_sale';
    case ForRent = 'for_rent';
    case SoldOut = 'sold_out';

    public function label(): string
    {
        return match ($this) {
            self::Ongoing => 'Ongoing',
            self::Completed => 'Completed',
            self::Upcoming => 'Upcoming',
            self::ForSale => 'For Sale',
            self::ForRent => 'For Rent',
            self::SoldOut => 'Sold Out',
        };
    }

    // Maps to the badge classes defined in the theme (gold / green / navy / grey).
    public function badge(): string
    {
        return match ($this) {
            self::Ongoing => 'badge-gold',
            self::Completed => 'badge-success',
            self::Upcoming, self::ForSale, self::ForRent => 'badge-navy',
            self::SoldOut => 'badge-muted',
        };
    }
}
