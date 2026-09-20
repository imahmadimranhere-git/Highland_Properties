<?php

namespace Database\Seeders;

use App\Enums\OwnershipFlag;
use App\Enums\ProjectStatus;
use App\Enums\UnitAvailability;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Developer;
use App\Models\DevelopmentUpdate;
use App\Models\Location;
use App\Models\PaymentPlan;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\UnitCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $developer = Developer::where('slug', 'summit-builders')->firstOrFail();
        $city = City::where('name', 'Islamabad')->firstOrFail();
        $location = Location::where('city_id', $city->id)->where('name', 'Gulberg Greens')->firstOrFail();
        $type = ProjectType::where('name', 'Apartments')->firstOrFail();
        $consultant = User::consultants()->first();

        $project = Project::updateOrCreate(
            ['slug' => 'highland-heights'],
            [
                'developer_id' => $developer->id,
                'city_id' => $city->id,
                'location_id' => $location->id,
                'project_type_id' => $type->id,
                'assigned_consultant_id' => $consultant?->id,
                'name' => 'Highland Heights',
                'ownership_flag' => OwnershipFlag::Marketed,
                'status' => ProjectStatus::Ongoing,
                'short_description' => 'A twelve-storey residence of one, two and three bedroom apartments in Gulberg Greens.',
                'description' => 'Highland Heights brings together generous layouts, double-glazed facades and a landscaped podium level. Each floor carries four units only, keeping circulation quiet and private. Handover is planned in phases, starting with the lower residential block.',
                'address' => 'Civic Centre Road, Gulberg Greens, Islamabad',
                'latitude' => 33.6100000,
                'longitude' => 73.1300000,
                'nearby_landmarks' => [
                    'Islamabad Expressway — 5 min',
                    'Gulberg Greens Country Club — 7 min',
                    'New Islamabad Airport — 30 min',
                ],
                'total_area' => '4.2 Kanal',
                'total_floors' => 12,
                'total_units' => 96,
                'completion_target' => now()->addYears(2)->startOfMonth()->toDateString(),
                'approvals' => 'CDA / Gulberg Greens approved building plan',
                'starting_price' => 12500000,
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 1,
                'meta_title' => 'Highland Heights, Gulberg Greens | Highland Properties',
                'meta_description' => 'One, two and three bedroom apartments in Gulberg Greens with a four-year payment plan.',
            ]
        );

        $project->amenities()->sync(
            Amenity::whereIn('slug', [
                'swimming-pool', 'gymnasium', 'mosque', 'standby-generator',
                'cctv-security', 'basement-parking', 'high-speed-elevators',
            ])->pluck('id')
        );

        $categories = [
            [
                'name' => 'Category A',
                'unit_type' => '1 Bed Apartment',
                'size_value' => 750,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'total_price' => 12500000,
                'availability' => UnitAvailability::Available,
                'sort_order' => 1,
                'plan' => [
                    'booking_amount' => 1250000,
                    'down_payment' => 1875000,
                    'installment_count' => 48,
                    'installment_frequency' => 'monthly',
                    'installment_amount' => 156250,
                    'possession_charges' => 875000,
                ],
            ],
            [
                'name' => 'Category B',
                'unit_type' => '2 Bed Apartment',
                'size_value' => 1150,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'total_price' => 18900000,
                'availability' => UnitAvailability::Limited,
                'sort_order' => 2,
                'plan' => [
                    'booking_amount' => 1890000,
                    'down_payment' => 2835000,
                    'installment_count' => 16,
                    'installment_frequency' => 'quarterly',
                    'installment_amount' => 787500,
                    'possession_charges' => 1323000,
                ],
            ],
            [
                'name' => 'Category C',
                'unit_type' => '3 Bed Corner Apartment',
                'size_value' => 1620,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'total_price' => 26500000,
                'availability' => UnitAvailability::Available,
                'sort_order' => 3,
                'plan' => [
                    'booking_amount' => 2650000,
                    'down_payment' => 3975000,
                    'installment_count' => 48,
                    'installment_frequency' => 'monthly',
                    'installment_amount' => 331250,
                    'possession_charges' => 1855000,
                ],
            ],
        ];

        foreach ($categories as $row) {
            $plan = $row['plan'];
            unset($row['plan']);

            $category = UnitCategory::updateOrCreate(
                ['project_id' => $project->id, 'name' => $row['name']],
                $row + ['size_unit' => 'sq ft']
            );

            PaymentPlan::updateOrCreate(
                ['unit_category_id' => $category->id],
                $plan + ['notes' => 'Prices are indicative and subject to confirmation at booking.']
            );
        }

        $updates = [
            [
                'title' => 'Ninth floor slab poured',
                'update_date' => now()->subDays(12)->toDateString(),
                'description' => 'The ninth floor slab was completed on schedule. Block work on floors six and seven is underway.',
            ],
            [
                'title' => 'Facade glazing samples approved',
                'update_date' => now()->subMonths(2)->toDateString(),
                'description' => 'Double-glazed unit samples were approved by the consultant after thermal testing.',
            ],
            [
                'title' => 'Excavation and raft foundation completed',
                'update_date' => now()->subMonths(8)->toDateString(),
                'description' => 'Raft foundation poured and cured. Structural work on the basement levels has started.',
            ],
        ];

        foreach ($updates as $update) {
            DevelopmentUpdate::updateOrCreate(
                ['project_id' => $project->id, 'title' => $update['title']],
                $update + ['is_published' => true]
            );
        }
    }
}
