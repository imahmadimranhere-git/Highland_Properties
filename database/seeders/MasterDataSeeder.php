<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\City;
use App\Models\Location;
use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Islamabad' => ['DHA Phase II', 'Bahria Town', 'Gulberg Greens', 'B-17'],
            'Rawalpindi' => ['Bahria Town Phase 8', 'Adiala Road'],
            'Lahore' => ['DHA Phase VI', 'Raiwind Road'],
        ];

        foreach ($cities as $cityName => $areas) {
            $city = City::firstOrCreate(['name' => $cityName]);

            foreach ($areas as $area) {
                Location::firstOrCreate(
                    ['city_id' => $city->id, 'slug' => str($area)->slug()->value()],
                    ['name' => $area]
                );
            }
        }

        foreach (['Apartments', 'Housing Society', 'Commercial Plaza', 'Farm House', 'Villas'] as $type) {
            ProjectType::firstOrCreate(['name' => $type]);
        }

        // icon = key of a symbol in the inline SVG sprite (no icon font is loaded).
        $amenities = [
            'Swimming Pool' => 'pool',
            'Gymnasium' => 'gym',
            'Kids Play Area' => 'play',
            'Mosque' => 'mosque',
            'Standby Generator' => 'power',
            'CCTV Security' => 'shield',
            'Basement Parking' => 'parking',
            'High Speed Elevators' => 'elevator',
            'Rooftop Garden' => 'garden',
            'Community Centre' => 'community',
        ];

        foreach ($amenities as $name => $icon) {
            Amenity::firstOrCreate(['name' => $name], ['icon' => $icon]);
        }
    }
}
