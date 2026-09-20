<?php

namespace App\Enums;

enum RoleSlug: string
{
    case SuperAdmin = 'super_admin';
    case SalesConsultant = 'sales_consultant';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::SalesConsultant => 'Sales Consultant',
        };
    }
}
