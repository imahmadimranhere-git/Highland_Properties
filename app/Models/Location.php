<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasSlug;

    protected $fillable = ['city_id', 'name', 'slug', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // The same society name can exist in two cities, so uniqueness is per city.
    protected function applySlugScope(Builder $query): void
    {
        $query->where('city_id', $this->city_id);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
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
