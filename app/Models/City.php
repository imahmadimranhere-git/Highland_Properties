<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasSlug;

    protected $fillable = ['name', 'slug', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
