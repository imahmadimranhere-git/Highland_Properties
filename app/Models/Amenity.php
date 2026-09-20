<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    use HasSlug;

    protected $table = 'amenities';

    protected $fillable = ['name', 'slug', 'icon', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
