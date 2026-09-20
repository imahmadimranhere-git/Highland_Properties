<?php

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\ConsultantTarget;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', RoleSlug::SuperAdmin->value)->firstOrFail();
        $consultantRole = Role::where('slug', RoleSlug::SalesConsultant->value)->firstOrFail();

        // Change this password immediately after the first login.
        User::updateOrCreate(
            ['email' => 'admin@highlandproperties.test'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Super Admin',
                'phone' => '+92 300 0000000',
                'designation' => 'Administrator',
                'password' => 'Admin@12345',
                'is_active' => true,
            ]
        );

        $consultant = User::updateOrCreate(
            ['email' => 'consultant@highlandproperties.test'],
            [
                'role_id' => $consultantRole->id,
                'name' => 'Ahmed Raza',
                'phone' => '+92 301 1111111',
                'designation' => 'Senior Sales Consultant',
                'password' => 'Consultant@12345',
                'is_active' => true,
            ]
        );

        ConsultantTarget::updateOrCreate(
            ['user_id' => $consultant->id, 'period' => ConsultantTarget::currentPeriod()],
            ['target_deals' => 5, 'target_amount' => 50000000],
        );
    }
}
