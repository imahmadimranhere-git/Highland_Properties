<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Developer extends Model
{
    use HasSlug;
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'logo_path', 'background', 'experience_years',
        'completed_projects', 'contact_person', 'phone', 'email', 'website',
        'address', 'is_active', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'experience_years' => 'integer',
        'completed_projects' => 'integer',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
