<?php

namespace Database\Seeders;

use App\Models\Developer;
use Illuminate\Database\Seeder;

class DeveloperSeeder extends Seeder
{
    public function run(): void
    {
        Developer::updateOrCreate(
            ['slug' => 'summit-builders'],
            [
                'name' => 'Summit Builders',
                'background' => 'Summit Builders has delivered residential and commercial projects across Punjab and the capital region for over two decades, with a record of on-time possession.',
                'experience_years' => 22,
                'completed_projects' => 14,
                'contact_person' => 'Bilal Khan',
                'phone' => '+92 51 7654321',
                'email' => 'info@summitbuilders.test',
                'website' => 'https://summitbuilders.test',
                'address' => 'F-7 Markaz, Islamabad',
                'is_active' => true,
                'meta_title' => 'Summit Builders | Developer Profile',
                'meta_description' => 'Two decades of delivered residential and commercial projects.',
            ]
        );
    }
}
