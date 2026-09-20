<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Order matters: master data and roles must exist before projects and users.
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            MasterDataSeeder::class,
            SettingSeeder::class,
            DeveloperSeeder::class,
            ProjectSeeder::class,
            ContentSeeder::class,
        ]);
    }
}
