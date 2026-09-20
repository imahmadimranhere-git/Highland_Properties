<?php

namespace App\Enums;

enum ReportType: string
{
    case SiteVisit = 'site_visit';
    case Sales = 'sales';
    case Booking = 'booking';
    case Expense = 'expense';
    case MonthlyPerformance = 'monthly_performance';

    public function label(): string
    {
        return match ($this) {
            self::SiteVisit => 'Site Visit',
            self::Sales => 'Sales',
            self::Booking => 'Booking',
            self::Expense => 'Expense',
            self::MonthlyPerformance => 'Monthly Performance',
        };
    }

    // These types describe a period, the rest describe a single day.
    public function usesPeriod(): bool
    {
        return $this === self::MonthlyPerformance;
    }
}
