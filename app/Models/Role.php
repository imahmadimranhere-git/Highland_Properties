<?php

namespace App\Models;

use App\Enums\RoleSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->slug === RoleSlug::SuperAdmin->value;
    }

    public function isConsultant(): bool
    {
        return $this->slug === RoleSlug::SalesConsultant->value;
    }
}
