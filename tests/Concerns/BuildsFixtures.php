<?php

namespace Tests\Concerns;

use App\Enums\RoleSlug;
use App\Models\City;
use App\Models\Developer;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Str;

/** Small builders so each test reads as the scenario it checks. */
trait BuildsFixtures
{
    protected function seedRoles(): void
    {
        $this->seed(RoleSeeder::class);
    }

    protected function admin(array $attributes = []): User
    {
        return $this->makeUser(RoleSlug::SuperAdmin, $attributes);
    }

    protected function consultant(array $attributes = []): User
    {
        return $this->makeUser(RoleSlug::SalesConsultant, $attributes);
    }

    protected function makeUser(RoleSlug $role, array $attributes = []): User
    {
        return User::create($attributes + [
            'role_id' => Role::where('slug', $role->value)->value('id'),
            'name' => 'Test ' . Str::random(5),
            'email' => Str::random(10) . '@example.test',
            'password' => 'Password123',
            'is_active' => true,
        ]);
    }

    protected function project(array $attributes = []): Project
    {
        $city = City::firstOrCreate(['name' => 'Islamabad']);
        $developer = Developer::firstOrCreate(['name' => 'Test Developer']);

        return Project::create($attributes + [
            'developer_id' => $developer->id,
            'city_id' => $city->id,
            'name' => 'Project ' . Str::random(6),
            'ownership_flag' => 'marketed',
            'status' => 'ongoing',
            'is_published' => true,
        ]);
    }

    protected function lead(array $attributes = []): Lead
    {
        return Lead::create($attributes + [
            'name' => 'Client ' . Str::random(4),
            'phone' => '03001234567',
            'status' => 'new',
            'source' => 'website',
        ]);
    }
}
